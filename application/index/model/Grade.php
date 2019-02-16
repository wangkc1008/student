<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
class Grade extends Model
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
    
	protected $dateFormat = 'Y年m月d日';
	
	protected $autoWriteTimestamp = true;
	protected $insert = [
        'is_delete'=>0,
	];
	// 创建时间字段
    protected $createTime = 'create_time';
    // 更新时间字段
    protected $updateTime = 'update_time';

    public function teacher(){
    	//一个班级对应一名老师
    	return $this->hasOne('teacher');
    }

    public function student(){
    	//一个班级对应多名学生
    	return $this->hasMany('student');
    }
}