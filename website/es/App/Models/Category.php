<?php
/**
 * Created by PhpStorm.
 * User: <243083741@qq.com>
 * Date: 2019/7/24
 * Time: 22:29
 */

namespace App\Models;


class Category extends Base
{
    public $tableName = "ln_categories";

	public function getCategoryByTitle($username) {

		if(empty($username)) {
			return [];
		}

		$this->db->where ("name", $username);
		$result = $this->db->getOne($this->tableName);
		return $result ?? [];
	}

	public function setPath($data)
	{

	}


	public function insertCategory($insert) {

		$result = $this->db->insert($this->tableName,$insert);
		$parent_id = isset($insert['parent_id']) ? $insert['parent_id'] : 0;
		if ($result){
			if ($parent_id == 0){
				$data['level'] = 0;
				$data['path'] = '-';
			}else{
				$arr = $this->db->where('id',$insert['parent_id'])->getOne($this->tableName);
				$data['level'] = $arr['level']+1;
				$data['path'] = $arr['path'].$insert['parent_id'].'-';
			}
			$this->db->where('id',$result)->update($this->tableName,$data);
		}
		return $result ? $result : null;
	}

}