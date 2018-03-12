<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Service\Customer;

use Praxigento\Downline\Api\Service\Customer\Search\Response as AResponse;
use Praxigento\Downline\Api\Service\Customer\Search\Response\Item as DItem;
use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Entity\Data\Customer as EDwnlCust;
use Praxigento\Downline\Repo\Query\Customer\Get as QBGetCustomer;

/**
 * Get suggestions for customers by key (name/email/mlm_id).
 */
class Search
    implements \Praxigento\Downline\Api\Service\Customer\Search
{
    const DEF_LIMIT = 10;

    /** @var \Praxigento\Downline\Repo\Query\Customer\Get */
    private $qbGetCustomer;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;

    public function __construct(
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        \Praxigento\Downline\Repo\Query\Customer\Get $qbGetCustomer
    ) {
        $this->repoDwnlCust = $repoDwnlCust;
        $this->qbGetCustomer = $qbGetCustomer;
    }

    /**
     * Convert database query result set to response object.
     *
     * @param array $db
     * @return DItem
     * @throws \Exception
     */
    private function convertDbToApi($db)
    {
        $result = new DItem();
        if ($db) {
            /* extract DB data */
            $custId = $db[QBGetCustomer::A_ID];
            $email = $db[QBGetCustomer::A_EMAIL];
            $nameFirst = $db[QBGetCustomer::A_NAME_FIRST];
            $nameLast = $db[QBGetCustomer::A_NAME_LAST];
            $mlmId = $db[QBGetCustomer::A_MLM_ID];
            $path = $db[QBGetCustomer::A_PATH];
            $country = $db[QBGetCustomer::A_COUNTRY];

            /* prepare response data */
            $pathFull = $path . $custId . Cfg::DTPS;

            /* compose response data */
            $result->setId($custId);
            $result->setEmail($email);
            $result->setNameFirst($nameFirst);
            $result->setNameLast($nameLast);
            $result->setMlmId($mlmId);
            $result->setCountry($country);
            $result->setPathFull($pathFull);
        }
        return $result;
    }

    /**
     * @param \Praxigento\Downline\Api\Service\Customer\Search\Request $req
     * @return \Praxigento\Downline\Api\Service\Customer\Search\Response
     * @throws \Exception
     */
    public function exec($req)
    {
        /** define local working data */
        $rootCustId = $req->getCustomerId();
        $key = $req->getSearchKey();
        $limit = $req->getLimit() ?? self::DEF_LIMIT;

        /** perform processing */
        $path = null;
        if ($rootCustId) {
            $path = $this->selectRootPath($rootCustId);
        }
        $items = $this->selectCustomers($key, $limit, $rootCustId, $path);

        /** compose result */
        $result = new AResponse();
        $result->setItems($items);
        return $result;
    }

    private function selectCustomers($key, $limit, $custId, $path)
    {
        $query = $this->qbGetCustomer->build();
        $conn = $query->getConnection();
        $searchBy = $conn->quote("%$key%");
        /* reset all WHERE clauses */
        $query->reset(\Zend_Db_Select::WHERE);
        /* add WHERE clause */
        $asCust = QBGetCustomer::AS_MAGE_CUST;
        $asDwnl = QBGetCustomer::AS_DWNL_CUST;
        $byFirst = "$asCust." . Cfg::E_CUSTOMER_A_FIRSTNAME . " LIKE $searchBy";
        $byLast = "$asCust." . Cfg::E_CUSTOMER_A_LASTNAME . " LIKE $searchBy";
        $byEmail = "$asCust." . Cfg::E_CUSTOMER_A_EMAIL . " LIKE $searchBy";
        $byMlmID = "$asDwnl." . EDwnlCust::ATTR_MLM_ID . " LIKE $searchBy";
        $where = "($byFirst) OR ($byLast) OR ($byEmail) OR ($byMlmID)";
        if ($custId) {
            /* restrict searching by root customer */
            $byOwnId = "$asDwnl." . EDwnlCust::ATTR_CUSTOMER_ID . '=' . (int)$custId;
            $quoted = $conn->quote($path . $custId . Cfg::DTPS . '%');
            $byPath = "$asDwnl." . EDwnlCust::ATTR_PATH . ' LIKE ' . $quoted;
            $where = "($where) AND (($byOwnId) OR ($byPath))";
        }
        $query->where($where);
        /* add LIMIT clause */
        $query->limit($limit);

        /* perform query & convert DB data to API data */
        $rows = $conn->fetchAll($query);
        $result = [];
        foreach ($rows as $row) {
            $item = $this->convertDbToApi($row);
            $result[] = $item;
        }
        return $result;
    }

    /**
     * Get root customer path to control
     * @param int $custId
     * @return string
     */
    private function selectRootPath($custId)
    {
        $result = null;
        $entity = $this->repoDwnlCust->getById($custId);
        if ($entity) $result = $entity->getPath();
        return $result;
    }
}