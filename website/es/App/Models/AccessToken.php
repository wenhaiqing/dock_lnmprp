<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-07-29
 * Time: 10:41
 */

namespace App\Models;


use App\HttpController\Common\Common;

class AccessToken extends  Base
{
	public $tableName = "ln_access_token";

	public function addAccessToken($data=[])
	{
		$data['ip']          = Common::getClientIp();
		$result              = $this->db->insert($this->tableName,$data);
		if( $result ){
			return $this->db->getInsertId();
		}
		return $result;
	}

	public function editAccessToken( $condition = [], $data = [] )
	{
		return $this->db->where($condition)->update($this->tableName, $data);
	}

	public function delAccessToken( $condition = [] )
	{
		return $this->db->where( $condition )->delete($this->tableName,1);
	}

	public function getAccessTokenInfo($condition = array(), $field = '*',$order = 'create_time desc') {
		$info = $this->db->where('jti',$condition['jti'],'=','and')
			->where('sub',$condition['sub'],'=','and')
			->where('exp',$condition['exp'],'<=','and')
			->where('is_invalid',0,'=','and')
			->where('is_logout',0,'=','and')
			->getOne($this->tableName);
		return $info ? $info : false;
	}

}