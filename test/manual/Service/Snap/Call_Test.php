<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Service\Snap;



include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseMockeryCase {

    public function test_getLastDate() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Service\Snap\Call');
        $req = new Request\GetLastDate();
        /** @var  $resp Response\GetLastDate */
        $resp = $call->getLastDate($req);
        $this->assertTrue($resp->isSucceed());
        $period = $resp->getLastDate();
        $this->assertNotNull($period);
    }

    public function test_getStateOnDate() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Service\Snap\Call');
        $req = new Request\GetStateOnDate();
        $req->setDatestamp('20151202');
        /** @var  $resp Response\GetStateOnDate */
        $resp = $call->getStateOnDate($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_calc() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Service\Snap\Call');
        $req = new Request\Calc();
        $req->setDatestampTo('20151219');
        /** @var  $resp Response\Calc */
        $resp = $call->calc($req);
        $this->assertTrue($resp->isSucceed());
    }

    public function test_extendMinimal() {
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var  $call \Praxigento\Downline\Service\Snap\Call */
        $call = $obm->get('Praxigento\Downline\Service\Snap\Call');
        $req = new Request\ExpandMinimal();
        $req->setTree([
            2  => 1,
            3  => 1,
            4  => 2,
            5  => 2,
            6  => 3,
            7  => 3,
            20 => 20,
            10 => 7,
            11 => 7,
            1  => 1,
            12 => 10
        ]);
        /** @var  $resp Response\Calc */
        $resp = $call->expandMinimal($req);
        $this->assertTrue($resp->isSucceed());
    }

}