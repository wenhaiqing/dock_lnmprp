<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:29
 */

namespace App\Models;


class User extends Base
{
    public $tableName = "ln_users";

    public function getUserByUsername($username) {

        if(empty($username)) {
            return [];
        }

        $this->db->where ("username", $username);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }

	public function test_mysql()
	{
		return $this->db->getOne('test');
    }


    public function insertUser($insert) {

        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $this->db->getInsertId() : null;
    }
}