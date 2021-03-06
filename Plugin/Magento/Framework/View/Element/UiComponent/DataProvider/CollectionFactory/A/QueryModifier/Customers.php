<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Plugin\Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory\A\QueryModifier;

use Praxigento\Downline\Config as Cfg;
use Praxigento\Downline\Repo\Data\Customer;

class Customers
{
    private const AS_FLD_COUNTRY_REG = 'prxgtDwnlCountryReg';
    private const AS_FLD_CUSTOMER_DEPTH = 'prxgtDwnlCustomerDepth';
    private const AS_FLD_CUSTOMER_REF = 'prxgtDwnlCustomerRef';
    private const AS_FLD_PARENT_ID = 'prxgtDwnlParentId';
    private const AS_FLD_PARENT_REF = 'prxgtDwnlParentRef';
    private const AS_TBL_CUST = 'prxgtDwnlCust';
    private const AS_TBL_PARENT_CUST = 'prxgtDwnlParentCust';

    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function addFieldsMapping(
        \Magento\Customer\Model\ResourceModel\Grid\Collection $collection
    ) {
        // reg. country
        $fieldAlias = self::AS_FLD_COUNTRY_REG;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_COUNTRY_CODE;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $collection->addFilterToMap("main_table.$fieldAlias", $fieldFullName);
        // depth
        $fieldAlias = self::AS_FLD_CUSTOMER_DEPTH;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_DEPTH;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $collection->addFilterToMap("main_table.$fieldAlias", $fieldFullName);
        // ref (mlm id)
        $fieldAlias = self::AS_FLD_CUSTOMER_REF;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $collection->addFilterToMap("main_table.$fieldAlias", $fieldFullName);
        // parent id
        $fieldAlias = self::AS_FLD_PARENT_ID;
        $fieldFullName = self::AS_TBL_CUST . '.' . Customer::A_PARENT_REF;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $collection->addFilterToMap("main_table.$fieldAlias", $fieldFullName);
        // parent ref (mlm id)
        $fieldAlias = self::AS_FLD_PARENT_REF;
        $fieldFullName = self::AS_TBL_PARENT_CUST . '.' . Customer::A_MLM_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        $collection->addFilterToMap("main_table.$fieldAlias", $fieldFullName);
    }

    public function populateSelect(
        \Magento\Customer\Model\ResourceModel\Grid\Collection $collection
    ) {
        $result = $collection->getSelect();
        /* LEFT JOIN `prxgt_dwnl_customer` AS `prxgtDwnlCust` */
        $tbl = [self::AS_TBL_CUST => $this->resource->getTableName(Customer::ENTITY_NAME)];
        $on = self::AS_TBL_CUST . '.' . Customer::A_CUSTOMER_REF . '='
            . Cfg::AS_MAIN_TABLE . '.' . Cfg::E_CUSTOMER_A_ENTITY_ID;
        $cols = [
            self::AS_FLD_COUNTRY_REG => Customer::A_COUNTRY_CODE,
            self::AS_FLD_CUSTOMER_REF => Customer::A_MLM_ID,
            self::AS_FLD_CUSTOMER_DEPTH => Customer::A_DEPTH,
            self::AS_FLD_PARENT_ID => Customer::A_PARENT_REF
        ];
        $result->joinLeft($tbl, $on, $cols);
        /* LEFT JOIN `prxgt_dwnl_customer` AS `prxgtDwnlParentCust` */
        $tbl = [self::AS_TBL_PARENT_CUST => $this->resource->getTableName(Customer::ENTITY_NAME)];
        $on = self::AS_TBL_PARENT_CUST . '.' . Customer::A_CUSTOMER_REF . '='
            . self::AS_TBL_CUST . '.' . Customer::A_PARENT_REF;
        $cols = [
            self::AS_FLD_PARENT_REF => Customer::A_MLM_ID
        ];
        $result->joinLeft($tbl, $on, $cols);
        return $result;
    }

}
