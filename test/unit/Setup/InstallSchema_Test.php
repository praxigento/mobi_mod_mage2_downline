<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Setup;

use Praxigento\Downline\Data\Entity\Change;
use Praxigento\Downline\Data\Entity\Customer;
use Praxigento\Downline\Data\Entity\Snap;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class InstallSchema_UnitTest extends \Praxigento\Core\Test\BaseCase\Setup\Schema
{

    /** @var  InstallSchema */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new InstallSchema(
            $this->mResource,
            $this->mToolDem
        );
    }

    public function test_install()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $setup->startSetup();
        $this->mSetup
            ->shouldReceive('startSetup')->once();
        // $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);
        $mDemPackage = $this->_mock(DataObject::class);
        $this->mToolDem
            ->shouldReceive('readDemPackage')->once()
            ->withArgs([\Mockery::any(), '/dBEAR/package/Praxigento/package/Downline'])
            ->andReturn($mDemPackage);
        // $demEntity = $demPackage->getData('package/Type/entity/Asset');
        $mDemPackage->shouldReceive('getData');
        //
        // $this->_toolDem->createEntity($entityAlias, $demEntity);
        //
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Customer::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Change::ENTITY_NAME, \Mockery::any()]);
        $this->mToolDem->shouldReceive('createEntity')->withArgs([Snap::ENTITY_NAME, \Mockery::any()]);
        // $setup->endSetup();
        $this->mSetup
            ->shouldReceive('endSetup')->once();
        /** === Call and asserts  === */
        $this->obj->install($this->mSetup, $this->mContext);
    }
}