<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Downline\Cli\Parent;

use Praxigento\Downline\Api\Service\Customer\Parent\Change\Request as ARequest;
use Praxigento\Downline\Api\Service\Customer\Parent\Change\Response as AResponse;

/**
 * Change parent for the customer.
 */
class Change
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_CUST_MLM_ID_NAME = 'customer';
    const OPT_CUST_MLM_ID_SHORTCUT = 'c';
    const OPT_PARENT_MLM_ID_NAME = 'parent';
    const OPT_PARENT_MLM_ID_SHORTCUT = 'p';
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Downline\Api\Service\Customer\Parent\Change */
    private $servParentChange;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Api\Service\Customer\Parent\Change $servParentChange
    ) {
        $manObj = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct(
            $manObj,
            'prxgt:downline:parent:change',
            'Change parent for the customer.'
        );
        $this->manTrans = $manTrans;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->servParentChange = $servParentChange;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_CUST_MLM_ID_NAME,
            self::OPT_CUST_MLM_ID_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            'MLM ID of the customer for whom parent being changed.'
        );
        $this->addOption(
            self::OPT_PARENT_MLM_ID_NAME,
            self::OPT_PARENT_MLM_ID_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            'MLM ID of the new parent.'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $def = $this->manTrans->begin();
        try {
            $output->writeln('<info>Command \'' . $this->getName() . '\':<info>');

            /** define local working data */
            $custMlmId = $input->getOption(self::OPT_CUST_MLM_ID_NAME);
            $parentMlmId = $input->getOption(self::OPT_PARENT_MLM_ID_NAME);
            $output->writeln("<info>Setup new parent '$parentMlmId' for customer '$custMlmId'.<info>");
            $cust = $this->daoDwnlCust->getByMlmId($custMlmId);
            $parent = $this->daoDwnlCust->getByMlmId($parentMlmId);
            $custId = $cust->getCustomerId();
            $parentId = $parent->getCustomerId();

            $req = new ARequest();
            $req->setCustomerId($custId);
            $req->setNewParentId($parentId);
            /** @var AResponse $resp */
            $resp = $this->servParentChange->exec($req);
            if ($resp->getErrorCode() != $resp::ERR_NO_ERROR) {
                $output->writeln('<info>Operation is failed. See log for details.<info>');
            }
            $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
            $this->manTrans->commit($def);
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '.<info>');
        } finally {
            $this->manTrans->end($def);
        }
    }


}