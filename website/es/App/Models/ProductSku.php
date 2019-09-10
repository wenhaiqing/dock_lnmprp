<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:29
 */

namespace App\Models;


use EasySwoole\EasySwoole\Logger;

class ProductSku extends Base
{
    public $tableName = "ln_product_skus";

	public function desStock(int $id,int $amount)
	{
		if ($amount<0){
			return '减库存数量不能小于0';
		}

//			$res = $this->db->where('id',$id,'=','and')
				//去掉下面的条件，因为当下面条件成立时，执行结果为true，与逻辑不符
				//虽然没有更新成功，但是返回true，暂不确定原因，待查
//			->where('stock',$amount,'>=')
//				->update($this->tableName,['stock'=>$this->db->dec($amount)]);
			$res = $this->db->rawQuery("UPDATE ln_product_skus set stock=stock-1 WHERE id='".$id."' AND stock>='".$amount."'");
			if (!$res){
//			Logger::getInstance()->log($this->db->where('id',$id)->getOne($this->tableName,'stock')['stock']);
				return '库存不足';
			}


    }

	public function addStock(int $id,int $amount)
	{
		if ($amount < 0){
			return '加库存不可小于0';
		}
		$this->db->where('id',$id)
			->update($this->tableName,['stock'=>$this->db->inc($amount)]);
    }

}


