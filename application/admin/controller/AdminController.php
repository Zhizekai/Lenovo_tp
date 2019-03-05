<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/3/3
 * Time: 11:14
 */

namespace app\admin\controller;


use think\Db;

class AdminController extends AdminBase
{

    public function admin_login () {

        $account = input('account',0,'intval');
//        管理员id
        $psw = input('password','','trim');
//        密码

        $res = Db::name('admin')
            ->where(['account'=>$account,'password'=>$psw])
            ->field('token,status')
            ->find();

        if ($res) {
            return $this->output_success(10010,$res,'管理员你好，登录成功');
        } else {
            return $this->output_success(10000,0,'你好像不是管理员，登录失败');
        }

    }

}