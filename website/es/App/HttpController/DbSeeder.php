<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-08-16
 * Time: 8:18
 */

namespace App\HttpController;


use App\Models\User;
use App\Util\Redis_ut;

class DbSeeder extends Base
{
	public function addUsers()
	{
//		$arr = array(
//			130,131,132,133,134,135,136,137,138,139,
//			144,147,
//			150,151,152,153,155,156,157,158,159,
//			176,177,178,
//			180,181,182,183,184,185,186,187,188,189,
//		);
//
//		for($i = 0; $i < 10000; $i++) {
//			$tmp[] = $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
//		}
//
//		$new_phone = array_unique($tmp);
//		for($i=0;$i<count($new_phone);$i++){
//			$data['phone'] = $new_phone[$i];
//			$data['password'] =password_hash('123456', PASSWORD_DEFAULT);
//			$data['username'] = $new_phone[$i];
//			$userModel = new User();
//			$userModel->insertUser($data);
//		}
		var_dump(Redis_ut::getInstance()->set('seckill_sku_22',100,7200));
//		var_dump(Redis_ut::getInstance()->setex('test',1000,10));
//		var_dump(Redis_ut::getInstance()->decr('test'));

	}
}