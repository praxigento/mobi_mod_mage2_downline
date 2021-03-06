<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Api\Web\Customer\Search;

/**
 * Search customers by some criteria (name, email, etc.) with additional attributes (MLM ID).
 */
interface ByKeyInterface
    extends \Praxigento\Core\Api\Web\Customer\Search\ByKeyInterface
{
    /**
     * @param \Praxigento\Downline\Api\Web\Customer\Search\ByKey\Request $request
     * @return \Praxigento\Downline\Api\Web\Customer\Search\ByKey\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}