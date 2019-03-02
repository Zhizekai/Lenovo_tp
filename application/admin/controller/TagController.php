<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/3/2
 * Time: 15:27
 */

namespace app\admin\controller;

use think\Db;

class TagController extends AdminBase
{

    /**
     * 管理员添加问题标签接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */


    public function add_tag () {

        $this->check_admin();

        $qid = input('qid',0,'intval');

        $tag_id = input('tag_id',0,'intval');

        $add = Db::name('question')
            ->where(['id'=>$qid])
            ->update(['tag_id'=>$tag_id]);

        if ($add) {
            return $this->output_success(10010,1,'标签问题成功');
        } else {
            return $this->output_success(10000,0,'标签问题失败');
        }

    }

    public function show_tag () {

        $this->check_admin();

        $res = Db::name('tag')->select();

        if ($res) {
            return $this->output_success(10010,1,'标签提取成功');
        } else {
            return $this->output_success(10000,0,'标签提取失败');
        }

    }

}