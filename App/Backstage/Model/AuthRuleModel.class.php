<?php
namespace Backstage\Model;
use Backstage\Model\BaseModel;
/**
 * 权限规则model
 */
class AuthRuleModel extends BaseModel{

	/**
	 * 删除数据
	 * @param	array	$map	where语句数组形式
	 * @return	boolean			操作是否成功
	 */
	public function deleteData($map){
		$count=$this
			->where(array('pid'=>$map['id']))
			->count();
		if($count!=0){
			return false;
		}
		$result=$this->where($map)->delete();
		return $result;
	}
	/**
	 * 查询数据，多层返回
	 * @param	array	$str	控制器
	 * @return	array			数组
	 */
	public function getMenuName($str){
		$map["ar1.name"] = $str;
		$list = $this
			     ->field('ar1.title as title1, ar2.title as title2, ar3.title as title3, ar4.title as title4')
				 ->alias('ar1')
				 ->join('__AUTH_RULE__ ar2 ON ar1.pid = ar2.id','LEFT')
				 ->join('__AUTH_RULE__ ar3 ON ar2.pid = ar3.id','LEFT')
				 ->join('__AUTH_RULE__ ar4 ON ar3.pid = ar4.id','LEFT')
				 ->where($map)
				 ->select();
		return $list;
	}




}
