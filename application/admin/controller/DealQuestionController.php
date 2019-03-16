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
     * 除了已删除的
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

//    public function index() {
//
//        $this->check_admin();
//
//        $page = input('page',1,'intval');
////        页面
//
//        $questions = Db::name('question')->alias('a')
//            ->join('user b','a.user_id=b.id')
//            ->where(['a.is_deleted'=>0])
//            ->order('status','asc')
//            ->field('a.question,a.answer,a.status,a.id,a.user_id,a.tag_id,a.show_number,a.views,a.isshow')
//            ->page($page,15)
//            ->select();
//
//        $count = Db::name('question')
//            ->where(['is_deleted'=>0])
//            ->count('id');
//
//
////        foreach ($questions as &$value){
////            $value['son']['question'] = $value['question'];
////            $value['son']['answer'] = $value['answer'];
////            $value['son']['id'] = $value['id'];
////            $value['son']['status'] = $value['status'];
////            unset($value['answer']);
////            unset($value['name']);
////            unset($value['status']);
////        }
//
//        if ($questions) {
//            return $this->output_success(10010,$questions,$count);
//        } else {
//            return $this->output_success(10000,0,'获取问题列表失败');
//        }
//
//    }

    /**
     * 解忧人回答问题接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function answer_question () {

        $aid = $this->check_admin();

        $qid = input('qid',0,'intval');
        $answer = input('answer','','trim');
//        $status = 1;
        $update = Db::name('question')
            ->where('id',$qid)
            ->update(['answer'=>$answer,'status'=>1,'admin_id'=>$aid]);

        if ($update) {
            return $this->output_success(10010,1,'回答成功');
        } else {
            return $this->output_success(10000,[],'回答失败');
        }

    }


    /**
     * 超管修改问题展示顺序接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function change_num () {

        $this->check_admin(1);

        $qid = input('qid',0,'intval');

        $num = input('num',0,'intval');

        $change = Db::name('question')
            ->where('id',$qid)
            ->update(['show_number'=>$num]);

        if ($change) {
            return $this->output_success(10000,1,'修改问题顺序成功');
        } else {
            return $this->output_success(10010,[],'问题顺序修改失败');
        }

    }


    /**
     * 搜索问题接口
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */


    public function search () {

        $admin_id = $this->check_admin();

        $where = [];

        $tag_id = input('tag_id',0,'intval');
//        问题标签id
        $status = input('status',0,'intval');
//        问题状态 0未回答 1未查看 2已查看
        $describe = input('describe','','trim');
//        问题描述
        if ($tag_id != 0) {
            $where['a.tag_id'] = $tag_id;

        }

        if ($status != 0) {
            $where['a.status'] = $status;
        }

        if (!empty($describe)) {
            $where['a.question'] = ['like','%'.$describe.'%'];
        }


        if ($admin_id != 1) {
            $where3  = 'a.who_admin=0 OR a.who_admin='.$admin_id;

        }
        else {
            $where3 = [];
        }


        $where['a.is_deleted'] = 0;

        $page = input('page',1,'intval');
        //        页面
//        if (!is_null($page)) {
        $questions = Db::name('question')->alias('a')
            ->join('user b','a.user_id=b.id')
            ->join('tags t','a.tag_id=t.id')
            ->where($where3)
            ->where(['t.status'=>1])
            ->where($where)
            ->order('a.id desc')
            ->field('a.question,a.answer,a.status,a.id,a.user_id,a.tag_id,a.show_number,a.views,a.isshow,t.tag,a.who_admin')
            ->page($page,15)
            ->select();
//        var_dump(Db::name('question')->getLastSql());





        $count = Db::name('question')->alias('a')
            ->join('user b','a.user_id=b.id')
            ->join('tags t','a.tag_id=t.id')
            ->where($where3)
            ->where(['t.status'=>1])
            ->where($where)
            ->count();


//        foreach ($questions as &$value){
//            $value['son']['question'] = $value['question'];
//            $value['son']['answer'] = $value['answer'];
//            $value['son']['id'] = $value['id'];
//            $value['son']['status'] = $value['status'];
//            unset($value['answer']);
//            unset($value['name']);
//            unset($value['status']);
//        }

            if ($questions&&$count) {
                return $this->output_success(10010,$questions,$count);
            } else {
                return $this->output_success(10000,[],0
                );
            }
//        } else {
//            $res = Db::name('question')
//                ->where($where)
//                ->field('question,answer,status,id,user_id,tag_id,show_number')
//                ->select();
//
//            if ($res) {
//                return $this->output_success(10010,$res,'搜索问题成功');
//            } else {
//                return $this->output_success(10000,0,'搜索问题失败');
//            }
//        }
    }


    /**
     * 超管上下架主页问题接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function change_show () {

        $this->check_admin(1);

        $qid = input('qid',0,'intval');

        $isshow = input('isshow',0,'intval');
//        0下架1上架主页

        $res = Db::name('question')
            ->where('id',$qid)
            ->update(['isshow'=>$isshow]);

        if ($res) {
            return $this->output_success(10010,1,'上下架问题成功');
        } else {
            return $this->output_success(10000,[],'上下架问题失败');
        }

    }





}