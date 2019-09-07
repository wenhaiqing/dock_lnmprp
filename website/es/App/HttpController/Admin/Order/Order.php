<?php
/**
 * Created by PhpStorm.
 * User: wenhaiqing
 * Date: 2019-08-13
 * Time: 8:59
 */

namespace App\HttpController\Admin\Order;


use App\HttpController\Base;
use App\HttpController\Jwt\AccessToken;
use App\Models\Address;
use App\Models\Product;
use App\Models\ProductSku;
use EasySwoole\EasySwoole\Logger;

class Order extends Base
{
	public function OrderModel()
	{
		return new \App\Models\Order();
	}

	public function addOrder(){
		//$res为模拟数据
		$res['address_id'] = 1;
		$res['remark'] = 'test';
		$res['item'] = array(
			array('sku_id'=>22,'amount'=>1),
			array('sku_id'=>23,'amount'=>2)
		);
		//检查商品库存，是否上架等因素
			$sku = $this->checkSku($res['item']);
			if ($sku){
				return $this->writeJson('1002',$sku);
			}
			//根据accesstoken获取userID
		$user_id = $this->getUserId();
			//获取用户地址
		$address = $this->OrderModel()->db->where('user_id',$user_id)
			->where('id',$res['address_id'])
			->getOne('ln_user_addresses');
		$address['full_address'] = $address['province'].$address['city'].
			$address['district'].$address['address'];
		if (!$address){
			return $this->writeJson('1001','未添加地址');
		}
		//开启事务执行下单逻辑
		$this->OrderModel()->db->startTransaction();
		try{
			$data = array(
				'address'      => [ // 将地址信息放入订单中
					'address'       => $address['full_address'],
					'zip'           => $address['zip'],
					'contact_name'  => $address['contact_name'],
					'contact_phone' => $address['contact_phone'],
				],
				'remark'=>$res['remark'],
				'total_amount' => 0,
				'user_id'=>$user_id,
			);
			$result = $this->OrderModel()->insertOrder($data);
			$totalAmount = 0;
			foreach ($res['item'] as $value) {
				$sku = $this->OrderModel()->db->where('id',$value['sku_id'])
					->getOne('ln_product_skus');
				// 创建一个 OrderItem 并直接与当前订单关联
				$item = [
					'amount' => $value['amount'],
					'price'  => $sku['price'],
					'product_id'=>$sku['product_id'],
					'product_sku_id'=>$sku['id'],
					'order_id'=>$result
				];
				$order_items = $this->OrderModel()->db->insert('ln_order_items',$item);
				if (!$order_items){
					$this->OrderModel()->db->rollback();
					return $this->writeJson('1003','下单失败');
				}
				$totalAmount += $sku['price'] * $value['amount'];
				//商品减库存的逻辑
				$skuMode = new ProductSku();
				$desc = $skuMode->desStock($value['sku_id'],$value['amount']);
				if ($desc){
					$this->OrderModel()->db->rollback();
					return $this->writeJson('1006',$desc);
				}

				$order_item = $this->OrderModel()->updateOrder($result,['total_amount'=>$totalAmount]);
				if (!$order_item){
					$this->OrderModel()->db->rollback();
					return $this->writeJson('1004','下单失败');
				}
			}
		}catch (\Exception $e){
			$this->OrderModel()->db->rollback();
			Logger::getInstance()->info($e->getMessage());
			return $this->writeJson('1005','下单失败');
		}finally{
			$this->OrderModel()->db->commit();
		}
		return $this->writeJson('200','下单成功');

	}

	/**
	 * 秒杀商品下单逻辑
	 */
	public function addSkillOrder()
	{
//		$this->OrderModel()->db->startTrace();
		//随机拒绝
//		if (random_int(0, 100) < (int)50) {
//			return $this->writeJson(1000,'参与的用户过多，请稍后再试');
//		}
		//$res为模拟数据
//		$res['address_id'] = 1;
		$res['address'] = array(
			'province' =>'ss',
			'city' => '23',
			'district'=>'dd',
			'address'=>'aa',
			'zip'=>'asdf',
			'contact_name'=>'asd',
			'contact_phone'=>'dsa'
		);
		$res['remark'] = 'test';
		$res['item'] = array(
			array('sku_id'=>22,'amount'=>1),
//			array('sku_id'=>23,'amount'=>2)
		);
		//根据accesstoken获取userID
		$user_id = $this->getUserId();
		//检查商品库存，是否上架等因素
		$sku = $this->checkSkillSku($res['item'],$user_id);
		if ($sku){
			return $this->writeJson('1002',$sku);
		}


		//获取用户地址由原来的从数据库中获取改为前端传过来
//		$address = $this->OrderModel()->db
////			->where('user_id',$user_id)
//			->where('id',$res['address_id'])
//			->getOne('ln_user_addresses');
		$address = $res['address'];

		if (!$address){
			return $this->writeJson('1001','未添加地址');
		}
		$address['full_address'] = $address['province'].$address['city'].
			$address['district'].$address['address'];
		//开启事务执行下单逻辑
		$this->OrderModel()->db->startTransaction();
		try{
			$data = array(
				'address'      => [ // 将地址信息放入订单中
					'address'       => $address['full_address'],
					'zip'           => $address['zip'],
					'contact_name'  => $address['contact_name'],
					'contact_phone' => $address['contact_phone'],
				],
				'remark'=>$res['remark'],
				'total_amount' => 0,
				'user_id'=>$user_id,
			);
			$result = $this->OrderModel()->insertOrder($data);
			if (!$result){
				$this->OrderModel()->db->rollback();
				return $this->writeJson('10031','下单失败');
			}
			$totalAmount = 0;
			foreach ($res['item'] as $value) {
				$sku = $this->OrderModel()->db->where('id',$value['sku_id'])
					->getOne('ln_product_skus');
				// 创建一个 OrderItem 并直接与当前订单关联
				$item = [
					'amount' => $value['amount'],
					'price'  => $sku['price'],
					'product_id'=>$sku['product_id'],
					'product_sku_id'=>$sku['id'],
					'order_id'=>$result
				];
				$order_items = $this->OrderModel()->db->insert('ln_order_items',$item);

				if (!$order_items){
					$this->OrderModel()->db->rollback();
					return $this->writeJson('1003','下单失败');
				}
				$totalAmount += $sku['price'] * $value['amount'];
				//商品减库存的逻辑
				$skuMode = new ProductSku();
				$desc = $skuMode->desStock($value['sku_id'],$value['amount']);
				if ($desc){
					$this->OrderModel()->db->rollback();
					return $this->writeJson('1006',$desc);
				}

				$order_item = $this->OrderModel()->updateOrder($result,['total_amount'=>$totalAmount]);
				if (!$order_item){
					$this->OrderModel()->db->rollback();
					return $this->writeJson('1004','下单失败');
				}
			}
		}catch (\Exception $e){
			$this->OrderModel()->db->rollback();
			Logger::getInstance()->info($e->getMessage());
			return $this->writeJson('1005','下单失败');
		}finally{
			$this->OrderModel()->db->commit();
		}
//		var_dump($this->OrderModel()->db->endTrace());
		return $this->writeJson('200','下单成功');
	}

	public function checkSku($item)
	{
		foreach ($item as $value){
			$sku = $this->OrderModel()->db->where('b.id',$value['sku_id'])
				->join('`ln_products` as a','a.id = b.product_id')
				->getOne('ln_product_skus as b','b.*,a.type,a.on_sale');
			if (!$sku){
				return '商品不存在';
			}
			if (!$sku['on_sale']){
				return '商品未上架';
			}
			if ($sku['stock'] === 0){
				return '商品已售完';
			}
			if ($value['amount'] > 0 && $value['amount'] > $sku['stock']) {
				return '该商品库存不足';
			}
		}
	}

	public function checkSkillSku($item,$user_id)
	{
		foreach ($item as $value){
			$sku = $this->OrderModel()->db->where('b.id',$value['sku_id'])
				->join('`ln_products` as a','a.id = b.product_id')
				->join('`ln_seckill_products` as c','c.product_id=b.product_id')
				->getOne('ln_product_skus as b','b.*,a.type,a.on_sale,c.start_at,c.end_at');

			if (!$sku){
				return '商品不存在';
			}
			if ($sku['type'] !== Product::TYPE_SECKILL) {
				return '该商品不支持秒杀';
			}
			if (strtotime($sku['start_at'])>time()) {
				return '秒杀尚未开始';
			}
			if (strtotime($sku['end_at'])<time()) {
				return '秒杀已经结束';
			}
			if (!$sku['on_sale']){
				return '商品未上架';
			}
			if ($sku['stock'] === 0){
				return '商品已售完';
			}
			if ($value['amount'] > 0 && $value['amount'] > $sku['stock']) {
				return '该商品库存不足';
			}

//			$order = $this->OrderModel()->db
//				// 筛选出当前用户的订单
//				->where('a.user_id', $user_id,'=','and')
//				->where('a.paid_at',null ,'<>','and')
//				// 或者未关闭的订单
//				->where('a.closed', 0,'=','or')
//				->join('`ln_order_items` as b','b.order_id=a.id')
//				->getOne('ln_orders as a');
			$order = $this->OrderModel()->db->rawQuery("SELECT  * FROM ln_orders as a  JOIN `ln_order_items` as b on b.order_id=a.id WHERE  a.user_id = '".$user_id."'  and (a.paid_at <> NULL or a.closed = 0)  LIMIT 1 FOR UPDATE");
			if ($order) {
				if ($order[0]['paid_at']) {
					return '你已经抢购了该商品';
				}
				return '你已经下单了该商品，请到订单页面支付';
			}
		}
	}
}