<?php

namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use think\Session;
use app\index\model\Grade as Grade_model;

class Grade extends Base
{
	public function gradeList(Request $request){
		$this->isLogin();
		$this->view->assign('title','班级列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
		$data = Grade_model::all();
		$this->view->count = Grade_model::count();
		foreach ($data as $value) {
			$item=[
				'id'=>$value['id'],
				'name'=>$value['name'],
				'length'=>$value['length'],
				'price'=>$value['price'],
				'status'=>$value['status'],
				'create_time'=>$value['create_time'],
				//用关联方法teacher属性方式访问teacher表中数据
				'teacher'=>isset($value->teacher->name)?($value->teacher->name):'<span style="color:red;">未分配</span>',
			];
			$gradeList[] = $item;
		}
		$this->view->assign('grade_list',$gradeList);
		return $this->view->fetch('grade_list');
	}

	public function setStatus(Request $request){
		$grade_id = $request->param('id');
		$result = Grade_model::get($grade_id);
		if($result->getData('status')==1){
			Grade_model::update(['status'=>0],['id'=>$grade_id]);
		}else{
			Grade_model::update(['status'=>1],['id'=>$grade_id]);
		}
	}

	public function gradeAdd(){
		$this->isLogin();
		$this->view->assign('title','添加班级');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
        return $this->view->fetch('grade_add');
	}

	public function addGrade(Request $request){
		$data = $request->param();
		$rule = [
			'name|课程名'=>'require',
			'length|学制'=>'require',
			'price|学费'=>'require',
		];

		$result = $this->validate($data,$rule);
		if(true===$result){
			$grade = Grade_model::create($request->param());
			if($grade===null){
				$status = 0;
				$message = '添加失败';
			}else{
				$status = 1;
				$message = '添加成功';
			}
		}else{
			$status = 2;
			$message = '请检查输入格式';
		}
		return ['status'=>$status,'message'=>$message];
	}
	
	public function deleteGrade(Request $request){
		$grade_id = $request->param('id');
		Grade_model::update(['is_delete'=>1],['id'=>$grade_id]);
		Grade_model::destroy($grade_id);
	}

	public function unDelete(){
		Grade_model::update(['delete_time'=>NULL],['is_delete'=>1]);
	}

	public function gradeEdit(Request $request){
		$this->isLogin();
		$grade_id = $request->param('id');
		$grade_info = Grade_model::get($grade_id);
		$grade_info['teacher'] = $grade_info->teacher->name;
		$this->view->assign('title','编辑班级');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
        $this->view->assign('grade_info',$grade_info);
        return $this->view->fetch('grade_edit');
	}

	public function editGrade(Request $request){
		$param = $request -> except('teacher');
		foreach ($param as $key=>$value) {
			if($value!==''){
				$data[$key] = $value;
			}
		}
		$status = 0;
		$message = '修改失败';
		$condition = ['id'=>$data['id']];
		$result = Grade_model::update($data,$condition);
		if(true==$result){
			$status = 1;
			$message = '修改成功';
		}
		return ['status'=>$status,'message'=>$message];
	}
}