<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:18
 */

namespace App\HttpController\Admin\Login;

use App\HttpController\Base;
use App\HttpController\Jwt\AccessToken;
use App\Models\AdminUser;
use App\Models\User;
use App\Util\Redis_ut;
use EasySwoole\Http\Request;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Validate\Validate;

class Login extends Base
{

	public function get_user()
	{
		$userModel = new AdminUser();
		return $userModel;
	}

	/**
	 * @return bool
	 */
	public function login()
	{
		$validate = new Validate();
		$validate->addColumn('phone')->required('用户名必填');
		$validate->addColumn('password')->required('密码必填');

		if ($this->validate($validate)) {
			$params = $this->request()->getRequestParam();

			$user = $this->get_user()->getUserByUsername($params['phone']);
			if (!$user) {
				return $this->writeJson(10001, '用户不存在');
			}

			if (!password_verify($params['password'], $user['password'])) {
				return $this->writeJson(10001, '密码输入不正确!');
			};

			$token = new AccessToken();
				$access_token = $token->createAccessToken('admin_'.$user['id'], time());

			Redis_ut::getInstance()->set('admin_access_token_'.$user['id'], $access_token, AccessToken::expire_interval);

			return $this->writeJson(200, '登录成功', $access_token);
		} else {
			return $this->writeJson(10001, $validate->getError()->__toString(), 'fail');
		}

	}

	/**
	 * @return bool
	 */
	public function register()
	{
		$validate = new Validate();
		$validate->addColumn('phone')->required('手机号必填')->length(11,'手机号格式错误');
		$validate->addColumn('password')->required('密码必填');
		$validate->addColumn('code')->required('验证码必填');

		if ($this->validate($validate)) {
			$params = $this->request()->getRequestParam();

			/**
			 * 从redis中取出code同时删除redis数据
			 */
			$codeCache = Redis_ut::getInstance()->get('Code_admin' . $params['phone']);

			if ($codeCache != $params['code']) {
				return $this->writeJson(10001, '验证码错误');
			}
			Redis_ut::getInstance()->del('Code_admin'.$params['phone']);

			$user = $this->get_user()->getUserByUsername($params['phone']);
			if ($user) {
				return $this->writeJson(10001, '用户名已存在');
			}

			$data = [
				'email' => isset($params['email']) ? $params['email'] : '',
				'phone' => $params['phone'],
				'username' => $params['phone'],
				'password' => password_hash($params['password'], PASSWORD_DEFAULT),
			];

			$user_id = $this->get_user()->insertUser($data);
			if (!$user_id) {
				return $this->writeJson(10001, '注册失败');
			}

			return $this->writeJson(200, '注册成功');
		} else {
			return $this->writeJson(10001, $validate->getError()->__toString(), 'fail');
		}
	}

	/**
	 * 根据手机号获取验证码
	 * @return bool
	 */
	public function getCode()
	{
		$validate = new Validate();
		$validate->addColumn('phone')->required('手机号必填');
		if ($this->validate($validate)){
			$params = $this->request()->getRequestParam();
			$phone = $params['phone'];
			$code = mt_rand(0000,9999);
			$res = Redis_ut::getInstance()->set('Code_admin'.$phone,$code);
			if ($res){
				return $this->writeJson(200,'获取成功',$code);
			}

		}else{
			return $this->writeJson(10001, $validate->getError()->__toString(), 'fail');
		}

	}

	/**
	 * 检查token是否合法
	 * @return bool
	 */
	public function check()
	{
		$header = $this->request()->getHeaders();
		$access_token = new AccessToken();
		$token = $access_token->getRequestTokenString($header);
		if ($token){
			$res = $access_token->checkAccessToken($token[0]);
			if ($res){
				return $this->writeJson(200,'验证成功',$res);
			}
		}
		return $this->writeJson(1001,'验证失败',false);

	}
}