define([
    "Praxigento_Core/js/grid/column/link"
], function (Column) {
    "use strict";

    return Column.extend({
        defaults: {
            /* @see \Praxigento\Downline\Ui\DataProvider\Grid\Account\Query::A_PARENT_MLM_ID */
            idAttrName: "parentMlmId",
            route: "/customer/downline/index/mlmId/"
        }
    });
});
