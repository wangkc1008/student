<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Student as Student_model;
class Student extends Base{

	public function studentList(){
		$this->isLogin();
		$this->view->assign('title','学生列表');
        $this->view->count = Student_model::count();
        //10条数据一页
        $studentList = Student_model::paginate(5);
        foreach ($studentList as  $value) {
        	$value->grade = $value->grade->name;
        }
        $this->view->assign('studentList',$studentList);
        return $this->view->fetch('student_list');
	}

	public function setStatus(){
		$student_id = input('id');
		$result = Student_model::get($student_id);
		if($result->getData('status')==1){
			Student_model::update(['status'=>0],['id'=>$student_id]);
		}else{
			Student_model::update(['status'=>1],['id'=>$student_id]);
		}
	}

	public function deleteStudent(Request $request){
		$student_id = $request->param('id');
		Student_model::update(['is_delete'=>1],['id'=>$student_id]);
		Student_model::destroy($student_id);
	}

	public function unDelete(){
		Student_model::update(['delete_time'=>NULL],['is_delete'=>1]);
	}

	public function studentAdd(){
		$this->isLogin();
		$this->view->assign('title','添加学生');
        $this->view->assign('keywords','php.cn');
        $this->view->assign('description','ThinkPHP5开发实战');

        $this->view->assign('gradeList',\app\index\model\Grade::all());
        return $this->view->fetch('student_add');
	}

	public function addStudent(Request $request){
		$data = $request->param();
		$rule = [
			'name'=>'require',
			'age'=>'require',
			'mobile'=>'require',
			'email'=>'require',
			'start_time'=>'require',
		];
		$result = $this->validate($data,$rule);
		if(true===$result){
			$student = Student_model::create($request->param());
			if(null===$student){
				$status = 0;
				$message = '添加失败';
			}else{
				$status = 1;
				$message = '添加成功';
			}
		}else{
			$status = 2;
			$message = '请检查输入项';
		}
		return ['status'=>$status,'message'=>$message];
	}

	public function studentEdit(Request $request){
		$this->isLogin();
		$student_id = $request->param('id');
		$student_info = Student_model::get($student_id);
		$student_info['grade'] = $student_info->grade->name;
		$this->view->assign('title','编辑教师');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
        $this->view->assign('gradeList',\app\index\model\Grade::all());
        $this->view->assign('student_info',$student_info);
        return $this->view->fetch('student_edit');
	}

	public function editStudent(Request $request){
		$student_info = $request->param();
		foreach ($student_info as $key => $value) {
			if($value!==''){
				$data[$key] = $value;
			}
		}
		$condition = ['id'=>$data['id']];
		$result = Student_model::update($data,$condition);
		if(true == $result){
			$status = 1;
			$message = '修改成功';
		}else{
			$status = 0;
			$message = '修改失败';
		}
		return ['status'=>$status,'message'=>$message];
	}

}