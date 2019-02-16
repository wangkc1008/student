<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Request;
class Base extends Controller
{
	protected function __initialize(){
		parent::_initialize();
	}
   //防止用户未登录，在index/index中调用
    protected function isLogin(){
    	if(!Session::has('user_info')){
    		$this->error('用户未登录，无权访问',url('user/login'));
    	}
    }
    //防止用户重复登陆，在user/login调用
    protected function alreadyLogin(){
    	if(Session::has('user_info')){
    		$this->error('当前用户已登陆',url('index/index'));
    	}
    }
}
