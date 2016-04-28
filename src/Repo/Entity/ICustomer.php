<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Downline\Repo\Entity;

use Praxigento\Core\Repo\IBaseRepo;
use Praxigento\Downline\Data\Entity\Customer as Entity;

interface ICustomer extends IBaseRepo
{
    /**
     * @param array|Entity $data
     * @return int
     */
    public function create($data);
    
    /**
     * @param int $id
     * @return Entity|bool
     */
    public function getById($id);

}