<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\EasySwoole\Config as GConfig;
use EasySwoole\RedisPool\Redis;
use EasySwoole\RedisPool\Config as RConfig;
use EasySwoole\MysqliPool\Mysql;
use EasySwoole\Mysqli\Config as MConfig;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        $configData = GConfig::getInstance()->getConf('REDIS');
        $config = new RConfig($configData);
	    $config->setOptions(['serialize'=>false]);
        $poolConf = Redis::getInstance()->register('redis',$config);
        $poolConf->setMaxObjectNum($configData['maxObjectNum']);
        $poolConf->setMinObjectNum($configData['minObjectNum']);
        $mysqlConfigData = GConfig::getInstance()->getConf('MYSQL');
        $mysqlConfig = new MConfig($mysqlConfigData);
        $mysqlPoolConf = Mysql::getInstance()->register('mysql',$mysqlConfig);
        $mysqlPoolConf->setMaxObjectNum($mysqlConfigData['maxObjectNum']);
		$mysqlPoolConf->setMinObjectNum($mysqlConfigData['minObjectNum']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
//	    $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
//		    if ($server->taskworker == false) {
//			    //每个worker进程都预创建连接
//			    PoolManager::getInstance()->getPool(Mysql::class)->preLoad(5);//最小创建数量
//		    }
//	    });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}