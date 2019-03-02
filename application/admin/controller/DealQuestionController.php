<?php
/**
 * Created by PhpStorm.
 * User: 10551
 * Date: 2019/2/28
 * Time: 20:43
 */

namespace app\admin\controller;

use think\Db;

class DealQuestionController extends AdminBase
{

    /**
     * 解忧人获取问题列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function index() {

        $page = input('page',1,'intval');
//        页面

        $questions = Db::name('question')->alias('a')
            ->join('user b','a.user_id=b.id')
            ->where(['a.is_deleted'=>0])
            ->order('status','asc')
            ->field('a.question,a.answer,b.name,a.status,a.id,a.user_id')
            ->page($page,15)
            ->select();

//        foreach ($questions as &$value){
//            $value['son']['question'] = $value['question'];
//            $value['son']['answer'] = $value['answer'];
//            $value['son']['id'] = $value['id'];
//            $value['son']['status'] = $value['status'];
//            unset($value['answer']);
//            unset($value['name']);
//            unset($value['status']);
//        }

        if ($questions) {
            return $this->output_success(10010,$questions,'获取问题列表成功');
        } else {
            return $this->output_error(10000,'获取问题列表失败');
        }

    }

    public function answer_question () {
        $qid = input('qid',0,'intval');
        $answer = input('answer','','trim');
        $status = 1;
        $update = Db::name('question')->where('id',$qid)->update(['answer'=>$answer,'status'=>$status]);

        if ($update) {
            return $this->output_success(10010,1,'回答成功');
        } else {
            return $this->output_success(10000,0,'回答失败');
        }

    }


    public function change_num () {

        $qid = input('qid',0,'intval');

        $num = input('num','','intval');

        $change = Db::name('question')
            ->where('id',$qid)
            ->update(['show_number'=>$num]);

        if ($change) {
            return $this->output_success(10000,'','修改问题顺序成功');
        } else {
            return $this->output_error(10010,'问题顺序修改失败');
        }

    }

    public function search () {

        $tag_id = input('tag_id',0,'intval');
//        问题标签id
        $status = input('status',0,'intval');
//        问题状态 0未回答 1未查看 2已查看
        $describe = input('describe',0,'trim');
//        问题描述
        $where = [];

        if ($tag_id == 0) {

        }

        $res = Db::name('question')
            ->where($where);


    }


    public function change_show () {

        $qid = input('qid',0,'intval');

        $isshow = input('isshow',0,'intval');
//        0下架1上架主页

        $res = Db::name('question')
            ->where('id',$qid)
            ->update(['isshow'=>$isshow]);

        if ($res) {
            return $this->output_success(10010,1,'上下架问题成功');
        } else {
            return $this->output_success(10000,0,'上下架问题失败');
        }

    }
}