<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Lib\Service;

use Praxigento\Downline\Lib\Service\Customer\Request;
use Praxigento\Downline\Lib\Service\Customer\Response;

interface ICustomer {
    /**
     * Add new customer to downline and new entry to change log.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     */
    public function add(Request\Add $request);

    /**
     * @param Request\ChangeParent $request
     *
     * @return Response\ChangeParent
     */
    public function changeParent(Request\ChangeParent $request);
}