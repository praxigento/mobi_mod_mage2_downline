<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Query\Snap\OnDate;

use Praxigento\Downline\Data\Entity\Snap as Entity;

/**
 * Build query to get downline tree snap on given date.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Def\Builder
{
    const AS_TBL_DWNL_SNAP = 'prxgtDwnlSnap';
    const AS_TBL_DWNL_SNAP_4_MAX = 'prxgtDwnlSnap4Max';
    const AS_TBL_DWNL_SNAP_MAX = 'prxgtDwnlSnapMax';
    const BIND_DATE = 'date';

    /**
     * BINDING:
     *   * date - date to get snap on;
     *
     *
     * SELECT
     * `prxgtDwnlSnap`.`customer_id`,
     * `prxgtDwnlSnap`.`parent_id`,
     * `prxgtDwnlSnap`.`depth`,
     * `prxgtDwnlSnap`.`path`
     * FROM `prxgt_dwnl_snap` AS `prxgtDwnlSnap`
     * LEFT JOIN (SELECT
     * `prxgtDwnlSnap4Max`.`customer_id`,
     * (MAX(`prxgtDwnlSnap4Max`.`date`)) AS `date_max`
     * FROM `prxgt_dwnl_snap` AS `prxgtDwnlSnap4Max`
     * WHERE (prxgtDwnlSnap4Max.date <= :date)
     * GROUP BY `prxgtDwnlSnap4Max`.`customer_id`) AS `prxgtDwnlSnapMax`
     * ON (prxgtDwnlSnapMax.customer_id = prxgtDwnlSnap.customer_id)
     * AND (prxgtDwnlSnapMax.date_max = prxgtDwnlSnap.date)
     * WHERE (prxgtDwnlSnapMax.date_max IS NOT NULL)
     *
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getSelectQuery(\Praxigento\Core\Repo\Query\IBuilder $qbuild = null)
    {
        $asSnap = self::AS_TBL_DWNL_SNAP;
        $asSnap4Max = self::AS_TBL_DWNL_SNAP_4_MAX;
        $asMax = self::AS_TBL_DWNL_SNAP_MAX;
        $tblSnap = $this->resource->getTableName(Entity::ENTITY_NAME);
        /* select MAX(date) from prxgt_dwnl_snap (internal select) */
        $q4Max = $this->conn->select();
        $colDateMax = 'date_max';
        $expMaxDate = new \Praxigento\Core\Repo\Query\Expression(
            'MAX(`' . $asSnap4Max . '`.`' . Entity::ATTR_DATE . '`)'
        );
        $q4Max->from([$asSnap4Max => $tblSnap], [Entity::ATTR_CUSTOMER_ID, $colDateMax => $expMaxDate]);
        $q4Max->group($asSnap4Max . '.' . Entity::ATTR_CUSTOMER_ID);
        $q4Max->where($asSnap4Max . '.' . Entity::ATTR_DATE . '<=:' . self::BIND_DATE);
        /* select from prxgt_dwnl_snap */
        $result = $this->conn->select();
        $result->from([$asSnap => $tblSnap], [
            Entity::ATTR_CUSTOMER_ID,
            Entity::ATTR_PARENT_ID,
            Entity::ATTR_DEPTH,
            Entity::ATTR_PATH
        ]);
        /* left join $q4Max */
        $on = '(' . $asMax . '.' . Entity::ATTR_CUSTOMER_ID . '=' . $asSnap . '.' . Entity::ATTR_CUSTOMER_ID . ')';
        $on .= ' AND (' . $asMax . '.' . $colDateMax . '=' . $asSnap . '.' . Entity::ATTR_DATE . ')';
        $result->joinLeft([$asMax => $q4Max], $on, []);
        /* where */
        $result->where($asMax . '.' . $colDateMax . ' IS NOT NULL');
        return $result;
    }
}