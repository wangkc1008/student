<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
class User extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	protected $auto = [
		'delete_time'=>NULL,
		'is_delete'=>1,
	];

	protected $insert = [
		'login_time'=>NULL,
		'login_count'=>0,
	];

	protected $update = [];

	protected $autoWriteTimestamp = true;
	// 创建时间字段
    protected $createTime = 'create_time';
    // 更新时间字段
    protected $updateTime = 'update_time';
	protected $dateFormat = 'Y年m月d日';
	public function getRoleAttr($value){
		$role = [
			0=>'管理员',
			1=>'超级管理员',
		];
		return $role[$value];
	}
	public function getStatusAttr($value){
		$status = [
			0=>'已停用',
			1=>'已启用',
		];
		return $status[$value];
	}

	public function setUserpasswdAttr($value){
		return md5($value);
	}

	public function getLogintimeAttr($value){
		return date('Y/m/d H:i',$value);
	}

}