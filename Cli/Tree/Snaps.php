<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Cli\Tree;

/**
 * Create snapshots for downline tree.
 */
class Snaps
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\Downline\Service\ISnap */
    protected $callSnap;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Downline\Service\ISnap $callSnap
    ) {
        parent::__construct(
            $manObj,
            'prxgt:downline:snaps',
            'Create snapshots for downline tree.'
        );
        $this->callSnap = $callSnap;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $output->writeln("<info>{$this->getName()}<info>");
        $req = new \Praxigento\Downline\Service\Snap\Request\Calc();
        $resp = $this->callSnap->calc($req);
        $succeed = $resp->isSucceed();
        if ($succeed) {
            $output->writeln('<info>Command is completed.<info>');
        } else {
            $output->writeln('<info>Command is failed.<info>');
        }

    }

}