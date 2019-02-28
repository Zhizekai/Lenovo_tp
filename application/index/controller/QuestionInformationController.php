<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/2/23
 * Time: 14:55
 */

namespace app\index\controller;


use think\Db;
use think\exception\DbException;

class QuestionInformationController extends Base
{
    public function question_index () {
        $page = input('page',1,'intval');
        $res = Db::name('question')->alias('a')->join('admin b','a.admin_id=b.id')->where(['a.is_deleted'=>0])->order('a.show_number')->page($page,15)->select();
        if ($res) {
            return $this->output_success(10010,$res,'获取问题列表成功');
        } else {
            return $this->output_error(10000,'获取问题列表失败');
        }
    }
}