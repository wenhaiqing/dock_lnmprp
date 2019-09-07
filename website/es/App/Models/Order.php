<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:29
 */

namespace App\Models;


class Order extends Base
{
	const REFUND_STATUS_PENDING = 'pending';
	const REFUND_STATUS_APPLIED = 'applied';
	const REFUND_STATUS_PROCESSING = 'processing';
	const REFUND_STATUS_SUCCESS = 'success';
	const REFUND_STATUS_FAILED = 'failed';

	const SHIP_STATUS_PENDING = 'pending';
	const SHIP_STATUS_DELIVERED = 'delivered';
	const SHIP_STATUS_RECEIVED = 'received';


	public static $refundStatusMap = [
		self::REFUND_STATUS_PENDING    => '未退款',
		self::REFUND_STATUS_APPLIED    => '已申请退款',
		self::REFUND_STATUS_PROCESSING => '退款中',
		self::REFUND_STATUS_SUCCESS    => '退款成功',
		self::REFUND_STATUS_FAILED     => '退款失败',
	];

	public static $shipStatusMap = [
		self::SHIP_STATUS_PENDING   => '未发货',
		self::SHIP_STATUS_DELIVERED => '已发货',
		self::SHIP_STATUS_RECEIVED  => '已收货',
	];

	const TYPE_NORMAL = 'normal';
	const TYPE_CROWDFUNDING = 'crowdfunding';
	const TYPE_SECKILL = 'seckill';

	public static $typeMap = [
		self::TYPE_NORMAL => '普通商品订单',
		self::TYPE_CROWDFUNDING => '众筹商品订单',
		self::TYPE_SECKILL => '秒杀商品订单',
	];
    public $tableName = "ln_orders";

    public function getUserByUsername($username) {

        if(empty($username)) {
            return [];
        }

        $this->db->where ("username", $username);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }

    public function insertOrder($insert) {
		$data['no'] = $this->findAvailableNo();
		$insert['address'] = json_encode($insert['address']);
		$insert = array_merge($insert,$data);
        $result = $this->db->insert($this->tableName,$insert);
        return $result ? $result : null;
    }

	public function updateOrder($id,$data)
	{
		$result = $this->db->where('id',$id)->update($this->tableName,$data);
		return $result ? $result : null;
	}
    

	//生成订单流水号
	public function findAvailableNo()
	{
		// 订单流水号前缀
		$prefix = date('YmdHis');
		for ($i = 0; $i < 10; $i++) {
			// 随机生成 6 位的数字
			$no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
			// 判断是否已经存在
			$res = $this->db->where('no',$no)->getOne($this->tableName);
			if (!$res) {
				return $no;
			}
		}
		\Log::warning('find order no failed');

		return false;
	}

	//生成退款订单号
	public static function getAvailableRefundNo()
	{
		do {
			//Uuid类可以用来生成大概率不重复的字符串
			$no = Uuid::uuid4()->getHex();
			//查询生成的退款订单号在数据库中是否存在
		} while (self::query()->where('refund_no', $no)->exists());

		return $no;
	}
}