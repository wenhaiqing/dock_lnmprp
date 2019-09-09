<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:29
 */

namespace App\Models;


class Address extends Base
{
    public $tableName = "ln_user_addresses";

    public function getAddressById(int $id) : ?array
    {
        if(empty($id)) {
            return [];
        }
        $this->db->where ("id", $id);
        $result = $this->db->getOne($this->tableName);
        $result['full_address'] = $result['province'].$result['city'].$result['district'].$result['address'];
        return $result ?? [];
    }

    public function insertUser($insert) {

        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $result : null;
    }
}