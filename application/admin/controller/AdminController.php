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

        $account = input('account', 0, 'intval');
//        管理员id
        $psw = input('password', '', 'trim');


//        密码

        $res = Db::name('admin')
            ->where(['account' => $account, 'password' => $psw])
            ->field('token,status')
            ->select();
        $res1 = $res[0]['token'];
        $res2 = $res[0]['status'];

        if ($res) {
            return $this->output_success(10010, $res1, $res2);
        } else {
            return $this->output_success(10000, 0, '你好像不是管理员，登录失败');
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


        $choice = Db::name('question')
            ->where('id', $qid)
            ->update(['who_admin' => $who_admin]);

        if ($choice) {
            return $this->output_success(10010, 1, '指定管理员成功');
        } else {
            return $this->output_success(10000, 0, '指定管理员失败');
        }

    }


}