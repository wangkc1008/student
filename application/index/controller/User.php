<?php

namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\User as User_model;
use think\Session;

class User extends Base
{
    public function login()
    {
    	$this->alreadyLogin();
        return $this->view->fetch();
    }

    public function checkLogin(Request $request){
    	$status = 0;
    	$result = '';

    	$data = $request->param();
    	$rule = [
    		'name|用户名'=>'require',
    		'password|密码'=>'require',
    		'verify|验证码'=>'require|captcha'
    	];
    	$msg = [
    		'name'=>['require'=>'用户名不能为空，请检查'],
    		'password'=>['require'=>'密码不能为空，请检查'],
    		'verify'=>[
    			'require'=>'验证码不能为空，请检查',
    			'captcha'=>'验证码错误'
    		]
    	];
    	$result = $this->validate($data,$rule,$msg);

    	if($result===true){

    		$map = [
    			'user_name'=>$data['name'],
    			'user_passwd'=>md5($data['password'])
    		];
    		$user = User_model::get($map);
    		if($user==null){
    			$result = '未找到该用户';
    		}else{
    			$status = 1;
    			$result = '验证通过，点击[确定]进入系统';
    			Session::set('user_id',$user->id);
    			Session::set('user_info',$user->getData());

                $user->setInc('login_count');
    		}

    	}

    	return json(['status'=>$status,'message'=>$result,'data'=>$data]);
    }

    public function logout(){
        User_model::update(['login_time'=>time()],['id'=>Session::get('user_id')]);
    	Session::delete('user_id');
    	Session::delete('user_info');
    	$this->success('注销登陆，正在返回',url('user/login'));
    }

    public function adminList(){
        $this->isLogin();
        $this->view->assign('title','管理员列表');
        $this->view->assign('keywords','教学管理系统');
        $this->view->assign('description','教学案例');

        $this->view->count = User_model::count();
        $username = Session::get('user_info.user_name');
        if($username=='admin'){
            $list = User_model::all();
        }else{
            $list = User_model::all(['user_name'=>$username]);
        }

        $this->view->assign('list',$list);
        return $this->view->fetch('admin_list');
    }

    public function setStatus(Request $request){
        $user_id = $request->param('id');
        $result = User_model::get($user_id);
        if($result->getData('status')==1){
            User_model::update(['status'=>0],['id'=>$user_id]);
        }else{
            User_model::update(['status'=>1],['id'=>$user_id]);
        }
    }

    public function adminAdd(){
        $this->isLogin();
        $this->view->assign('title','添加管理员');
        $this->view->assign('keywords','php.cn');
        $this->view->assign('description','PHP中文网ThinkPHP5开发实战课程');
        return $this->view->fetch('admin_add');
    }

    public function deleteUser(Request $request){
        $user_id = $request->param('id');
        User_model::update(['is_delete'=>1],['id'=>$user_id]);
        User_model::destroy($user_id);
    }

    public function adminEdit(Request $request){
        $this->isLogin();
        $user_id = $request->param('id');
        $result = User_model::get($user_id);
        $this->view->assign('title','编辑管理员信息');
        $this->view->assign('keywords','php.cn');
        $this->view->assign('description','ThinkPHP5开发实战课程');
        $this->view->assign('user_info',$result->getData());
        return $this->view->fetch('admin_edit');
    }
    public function editUser(Request $request){
        $param = $request->param();
        foreach ($param as $key => $value) {
            if($value!==''){
                $data[$key] = $value;
            }
        }
        $condition = ['id'=>$data['id']];
        $result = User_model::update($data,$condition);
        //如果是admin用户,更新当前session中用户信息user_info中的角色role,供页面调用
        if(Session::get('user_info.user_name')=='admin'){
            Session::set('user_info.role',$data['role']);
        }
        if(Session::get('user_info.user_name')!=='admin'){
            Session::set('user_info.user_name',$data['user_name']);
        }
        if(true==$result){
            $status = 1;
            $message = '修改成功';
        }else{
            $status = 0;
            $message = '修改失败';
        }
        return ['status'=>$status,'message'=>$message];
    }

    public function unDelete(){
        //设置软删除字段
        //只有该字段为NULL,该字段才会显示出来
        User_model::update(['delete_time'=>NULL],['is_delete'=>1]);
    }

    public function checkUserName(Request $request){
        $name = trim($request->param('name'));
        if(!empty($name)&&strlen($name)>=4&&strlen($name)<=12){
            $status = 1;
            $message = '用户名可用';
            if(User_model::get(['user_name'=>$name])){
                $status = 0;
                $message = '该用户名已存在';
            }
        }else{
            $status = 2;
            $message = '请检查输入用户名';
        }
         return ['status'=>$status,'message'=>$message];
    }

    public function checkUserEmail(Request $request){
        $email = trim($request->param('email'));
        if(!empty($email)){
            $status = 1;
            $message = '邮箱可用';
            if(User_model::get(['email'=>$email])){
                $status = 0;
                $message = '该邮箱已被使用';
            }
        }else{
            $status = 2;
            $message = '邮箱不能为空';
        }
        return ['status'=>$status,'message'=>$message];
    }

    public function addUser(Request $request){
        $data = $request->param();
        $rule = [
            'user_name|用户名'=>'require|min:4|max:12',
            'user_passwd|密码'=>'require|min:6|max:10',
            'email|邮箱'=>'require|email',
        ];
        $result = $this->validate($data,$rule);
        if($result === true){
            $user = User_model::create($request->param());
            if($user === null){
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

}
