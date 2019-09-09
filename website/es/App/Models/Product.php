<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:29
 */

namespace App\Models;


class Product extends Base
{
	const TYPE_SECKILL = 'seckill';

	public static $typeMap = [
        self::TYPE_SECKILL => '秒杀商品',
    ];
    public $tableName = "ln_products";

    public function getProductByTitle($username) {

        if(empty($username)) {
            return [];
        }

        $this->db->where ("title", $username);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }


    public function insertProduct($insert) {

        $result = $this->db->insert($this->tableName,$insert);

        return $result ? $result : null;
    }

	public function updateProduct($id,$data)
	{
		$result = $this->db->where('id',$id)->update($this->tableName,$data);
		return $result ? $result : null;
    }
}


