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

    /**
     * 管理员登录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function admin_login()
    {

        $account = input('account', 0, 'trim');
//        管理员id
        $psw = input('password', '', 'trim');

//        密码

        $res = Db::name('admin')
            ->where(['account' => $account, 'password' => $psw ])
            ->where('status','<',2)
            ->find();

        if ($res) {
//            $res1 = $res[0]['token'];
//            $res2 = $res[0]['status'];
            $time = time();
            $token = sha1(time().rand(55,9999).'lxlcz');
            $add = Db::name('admin')
                ->where(['account' => $account])
                ->update(['token'=>$token,'expire'=>$time]);

            if ($add) {
                $sta = Db::name('admin')
                    ->where(['account' => $account,])
                    ->field('status')
                    ->find();
                return $this->output_success(10010, $token, $sta);
            } else {
                return $this->output_success(10009,[],'token生成失败');
            }
        } else {
            return $this->output_success(10000, [], '你可能被冻结或者账号密码错误，无法登陆');
        }

    }

    /**
     * 超管指定答题人
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function choice_who()
    {

        $this->check_admin(1);

        $who_admin = input('who_admin', 0, 'intval');

        $qid = input('qid', 0, 'intval');

        $check = Db::name('admin')->where(['id'=>$who_admin])->field('status')->find();
        if ($check['status'] == 2) {
            return $this->output_success(10008,[],'该管理员被冻结');
        } else {
            $choice = Db::name('question')->alias('q')->join('admin a','q.admin_id=a.id')
                ->where(['q.id'=>$qid])
                ->update(['q.who_admin' => $who_admin]);


            if ($choice) {
                return $this->output_success(10010, 1, '指定管理员成功');
            } else {
                return $this->output_success(10000, [], '该管理员无法指定');
            }
        }



    }


}