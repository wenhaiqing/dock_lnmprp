<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:51
 */

namespace App\Models;


use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\MysqliPool\Mysql;

class Base
{
    public $db;

    public function __construct()
    {
        $this->db = Mysql::getInstance()->pool('mysql')::defer();
    }

    protected function getDb()
    {
        return $this->db;
    }
}