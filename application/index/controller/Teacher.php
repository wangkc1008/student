<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Teacher as Teacher_model;
class Teacher extends Base{
	public function teacherList(){
		$this->isLogin();
		$this->view->assign('title','教师列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
        $this->view->count = Teacher_model::count();
        $data = Teacher_model::all();
        foreach ($data as $key => $value) {
        	//一
        	$item=[
				'id'=>$value['id'],
				'name'=>$value['name'],
				'degree'=>$value['degree'],
				'mobile'=>$value['mobile'],
				'school'=>$value['school'],
				'status'=>$value['status'],
				'hiredate'=>$value['hiredate'],
				//用关联方法teacher属性方式访问teacher表中数据
				'grade'=>isset($value->grade->name)?($value->grade->name):'<span style="color:red;">未分配</span>',
			];
			$teacherList[] = $item;
			//二
			// $value->grade = isset($value->grade->name)?($value->grade->name):'<span style="color:red;">未分配</span>';
        }
        $this->view->assign('teacherList',$teacherList);
        return $this->view->fetch('teacher_list');
	}

	public function setStatus(Request $request){
		$teacher_id = $request->param('id');
		$result = Teacher_model::get($teacher_id);
		if($result->getData('status')==1){
			Teacher_model::update(['status'=>0],['id'=>$teacher_id]);
		}else{
			Teacher_model::update(['status'=>1],['id'=>$teacher_id]);
		}
	}

	public function deleteTeacher(Request $request){
		$teacher_id = $request->param('id');
		Teacher_model::update(['is_delete'=>1],['id'=>$teacher_id]);
		Teacher_model::destroy($teacher_id);
	}

	public function unDelete(){
		Teacher_model::update(['delete_time'=>NULL],['is_delete'=>1]);
	}

	public function teacherAdd(){
		$this->isLogin();
		$this->view->assign('title','添加教师');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
        $this->view->assign('gradeList',\app\index\model\Grade::all());
        return $this->view->fetch('teacher_add');
	}

	public function addTeacher(Request $request){
		$data = $request->param();
		$rule = [
			'name'=>'require',
			'school'=>'require',
			'mobile'=>'require',
			'hiredate'=>'require'
		];
		$result = $this->validate($data,$rule);

		if(true===$result){
			$teacher = Teacher_model::create($request->param());
			if(null===$teacher){
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

	public function teacherEdit(Request $request){
		$this->isLogin();
		$teacher_id = $request->param('id');
		$teacher_info = Teacher_model::get($teacher_id);
		$teacher_info['grade'] = $teacher_info->grade->name;
		$this->view->assign('title','编辑教师');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');
        $this->view->assign('gradeList',\app\index\model\Grade::all());
        $this->view->assign('teacher_info',$teacher_info);
        return $this->view->fetch('teacher_edit');
	}

	public function editTeacher(Request $request){
		$teacher_info = $request->param();
		foreach ($teacher_info as $key => $value) {
			if($value!==''){
				$data[$key] = $value;
			}
		}
		$condition = ['id'=>$data['id']];
		$result = Teacher_model::update($data,$condition);
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