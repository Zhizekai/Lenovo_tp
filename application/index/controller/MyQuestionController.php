<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/2/23
 * Time: 15:15
 */

namespace app\index\controller;

use think\Db;
use think\exception\DbException;

class MyQuestionController extends Base
{

    public function my_question_index () {
        $page = input('page',1,'intval');
        $token = input('token','','trim');
        $uid=$this->lenovo_getuid($token);
        $res = Db::name('question')->alias('a')->join('admin b','a.admin_id=b.id')->where(['a.is_deleted'=>0,'a.user_id'=>$uid])->order('a.show_number')->page($page,15)->select();
        if ($res) {
            return $this->output_success(10010,$res,'获取我的问题列表成功');
        } else {
            return $this->output_error(10000,'获取我的问题列表失败');
        }
    }

    public function change_status () {
        $status = input('status',1,'intval');
        $token = input('token','','trim');
        $qid = input('qid','','intval');
        $uid=$this->lenovo_getuid($token);
        $res = Db::name('question')->where(['user_id'=>$uid,'id'=>$qid])->update(['status'=>$status]);
        if (!$res) {
            return $this->output_error(10000,'我的问题修改失败');
        }
    }
}