<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-07-15
 * Time: 8:58
 */

namespace App\HttpController;


use App\Models\User;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Mysqli\Config;
use EasySwoole\Mysqli\Mysqli;
use EasySwoole\RedisPool\Redis;
use Firebase\JWT\JWT;

class Index extends Base
{
	public function index()
	{
//		$this->response()->write('helloworld');
		$config = \EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL');

		$conf = new \EasySwoole\Mysqli\Config($config);

		$db = new Mysqli($conf);

//		$data = $db->get('test');

		$arr = [
			'name'=>'test',
			'age'=>12
		];
//		$insert_id = $db->insert('test',$arr);
//		var_dump($insert_id);

		$update_id = $db->where('id',1)->update('test',['name'=>'test1']);
		var_dump($update_id);
	}

	public function login()
	{
		/**
		 * 测试mysql 连接池
		 */
//		$db = new User();
//        var_dump($db->test_mysql());
//		$res = $db->test_mysql();
//		$res = $this->cfgValue('MYSQL');
		/**
		 * 测试原生mysqli
		 */
//		$mysql = new Mysqli(new Config($this->cfgValue('MYSQL')));
//		$res = $mysql->getOne('test');
		/**
		 * 测试redis连接池
		 */
//	    $redis = Redis::getInstance()->pool('redis')::defer();
//	    $redis->set('name','wenhaiqing');
//
//	    var_dump($redis->get('name'));
		/**
		 * 测试生成token
		 */
		$key = '344'; //key
		$time = time(); //当前时间
		$token = [
			'iss' => 'http://www.helloweba.net', //签发者 可选
			'aud' => 'http://www.helloweba.net', //接收该JWT的一方，可选
			'iat' => $time, //签发时间
			'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
			'exp' => $time+10, //过期时间,这里设置2个小时
		];
//		$res = JWT::encode($token, $key); //输出Token
		/**
		 * 测试验证token
		 */
		$jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC93d3cuaGVsbG93ZWJhLm5ldCIsImF1ZCI6Imh0dHA6XC9cL3d3dy5oZWxsb3dlYmEubmV0IiwiaWF0IjoxNTY0MTA1NzIyLCJuYmYiOjE1NjQxMDU3MjIsImV4cCI6MTU2NDEwNTczMn0.-M-Lq5tnlTDRPxVgQThyjRoXgHItZs5cvKQP8YMHx14"; //签发的Token
		try {
			JWT::$leeway = 60;//当前时间减去60，把时间留点余地
			$decoded = JWT::decode($jwt, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
			$arr = (array)$decoded;
			print_r($arr);
		} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
			echo $e->getMessage();
		}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
			echo $e->getMessage();
		}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
			echo $e->getMessage();
		}catch(\Exception $e) {  //其他错误
			echo $e->getMessage();
		}


		return $this->writeJson('200','','');
	}

	public function userInfo()
	{
		var_dump('userinfo');
		$data = [
			'id'=> '4291d7da9005377ec9aec4a71ea837f',
		    'name'=> '天野远子',
		    'username'=> 'admin',
		    'password'=> '',
		    'avatar'=> '/avatar2.jpg',
		    'status'=> 1,
		    'telephone'=> '',
		    'lastLoginIp'=> '27.154.74.117',
		    'lastLoginTime'=> 1534837621348,
		    'creatorId'=> 'admin',
		    'createTime'=> 1497160610259,
		    'merchantCode'=> 'TLif2btpzg079h15bk',
		    'deleted'=> 0,
		    'roleId'=> 'admin',
		    'role'=> ["id"=>1]
		];
		return $this->writeJson('200',$data);
	}
}