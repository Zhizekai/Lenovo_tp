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
        $token = input('token','','trim');
        $uid=$this->lenovo_getuid($token);
        $res = Db::name('question')->alias('a')->join('admin b','a.admin_id=b.id')->where(['is_deleted'=>0,'id'=>$uid])->order('show_number')->paginate(15);
        if ($res) {
            return $this->output_success(10010,$res,'获取我的问题列表成功');
        } else {
            return $this->output_error(10000,'获取我的问题列表失败');
        }
    }
}