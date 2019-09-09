<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-07-17
 * Time: 9:49
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\AbstractInterface\Controller;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Router extends AbstractRouter
{
	public function initialize(RouteCollector $routeCollector)
	{
		$this->setGlobalMode(true);//只匹配Router.php中的控制器方法响应,将不会执行框架的默认解析

		/*
		 * admin后台路由
		 */
		$routeCollector->get('/test','/Index');
		$routeCollector->get('/tests','/Index/login');
		$routeCollector->post('/user/info','/Index/userInfo');
		$routeCollector->post('/admin/get_code','/Admin/Login/Login/getCode');
		$routeCollector->post('/admin/register','/Admin/Login/Login/register');
		$routeCollector->post('/admin/login','/Admin/Login/Login/login');
		$routeCollector->get('/admin/check','/Admin/Login/Login/check');
		$routeCollector->post('/admin/add_category','/Admin/Product/Category/addCategory');

		/*
		 * api前台路由
		 */
		$routeCollector->post('/api/get_code','/Api/Login/Login/getCode');
		$routeCollector->post('/api/register','/Api/Login/Login/register');
		$routeCollector->get('/api/login','/Api/Login/Login/test_login');
		$routeCollector->post('/api/login','/Api/Login/Login/login');
		$routeCollector->get('/api/check','/Api/Login/Login/check');
		$routeCollector->post('/api/order','/Admin/Order/Order/addOrder');
		$routeCollector->post('/api/skillorder','/Admin/Order/Order/addSkillOrder');

		/*
		 * 测试路由和添加模拟数据路由
		 */
//				$routeCollector->post('/admin/testadd','/Admin/Product/Category/testadd');
		$routeCollector->post('/admin/test_add_p','/Admin/Product/Product/test_add_p');
		$routeCollector->post('/api/test_add_user','/DbSeeder/addUsers');

	}
}

