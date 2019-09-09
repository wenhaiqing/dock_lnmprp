<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-08-10
 * Time: 15:42
 */

namespace App\Util;


use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\RedisPool\Redis;

class Redis_ut
{
	use Singleton;

	protected $redis_obj;

	public function __construct()
	{
		$this->redis_obj = Redis::getInstance()->pool('redis')::defer();

		$this->redis_obj->select(Config::getInstance()->getConf('REDIS.database'));
	}

	public function set($key,$value,$expire=null)
	{
		return $this->redis_obj->set($key,$value,$expire);
	}

	public function get($key)
	{
		return $this->redis_obj->get($key);
	}

	public function del($key)
	{
		return $this->redis_obj->del($key);
	}

	public function decr($key)
	{
		return $this->redis_obj->decr($key);
	}

	public function incr($key)
	{
		return $this->redis_obj->incr($key);
	}

	public function setex($key,$timeout,$value)
	{
		return $this->redis_obj->setex($key,$timeout,$value);
	}
}