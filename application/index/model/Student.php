<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
class Student extends Model{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;
	protected $dateFormat = 'Y/m/d';
	protected $type =[
		'start_time' =>'timestamp',
	];
	protected $insert = [
		'is_delete'=>0,
	];
	public function getSexAttr($value){
		$sex = [
			0=>'男',
			1=>'女',
		];
		return $sex[$value];
	}
	public function grade(){
		return $this->belongsTo('grade');
	}
}