<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;

class Teacher extends model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	protected $dateFormat = 'Y/m/d';
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;
	protected $type = [
		'hiredate'=>'timestamp',
	];
	protected $insert = [
		'is_delete'=>0,
	];

	public function grade(){
		return $this->belongsTo('Grade');
	}

	public function getDegreeAttr($value){
		$degree = [
			1=>'专科',
			2=>'本科',
			3=>'研究生',
		];
		return $degree[$value];
	}
}