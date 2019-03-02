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
use think\Request;

class QuestionInformationController extends Base
{
    /**
     * 问题列表
     * @return array
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */

    public function question_index () {

//        $type = input('type',0,'intval'); //  0:问题列表   1：我的问题

        $page = input('page',1,'intval');

        $token = !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

//        当前问题页面
        $where = [];
        $order = 'a.show_number';
        if (!is_null($token)){

            $deal = explode(" ",$token);
            $token = $deal[1];
            $uid=$this->lenovo_getuid($token);

            if (empty($uid)) {
                return $this->output_error(10009,'用户信息错误');
            }
            $where['a.user_id'] = $uid;

            $order = 'a.status asc';
        } else {
//            $where['show_number'] = ['>',0];
            $where['a.isshow'] = 1;
        }


        $questions = Db::name('question')->alias('a')
            ->join('admin b','a.admin_id=b.id')
            ->where(['a.is_deleted'=>0])
            ->where($where)
            ->order($order)
            ->order('a.show_number asc')
            ->field('a.question,a.answer,b.name,a.status,a.id')
            ->page($page,15)
            ->select();

        foreach ($questions as &$value){
            $value['son']['question'] = $value['question'];
            $value['son']['answer'] = $value['answer'];
            $value['son']['name'] = $value['name'];
            $value['son']['status'] = $value['status'];
            unset($value['answer']);
            unset($value['name']);
            unset($value['status']);
        }



        if ($questions) {
            return $this->output_success(10010,$questions,'获取我的问题列表成功');
        } else {
            return $this->output_error(10000,'获取我的问题列表失败');
        }

    }

    /**
     * 修改问题状态
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function change_status () {
        $status = 2;
//        问题状态
        $token = !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        $qid = input('qid','','intval');
//        问题id

        if (!is_null($token)){
            $deal = explode(' ',$token);
            $token = $deal[1];

            $uid=$this->lenovo_getuid($token);
        }

        if (empty($uid)) {
            return $this->output_error(10009,'请先登陆');
        }

        $res = Db::name('question')->where(['user_id'=>$uid,'id'=>$qid])->update(['status'=>$status]);
        if (!$res) {
            return $this->output_error(10000,'我的问题修改失败');
        } else {
            return $this->output_success(10010,$qid,'我的问题修改成功');
        }
    }


    public function add_view () {

        $qid = input('qid',0,'intval');
//        问题id

        $date_time = strtotime(date('Y-m-d',time()));

//        $token = !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
//
//        $where = [];
//        if (!is_null($token)){
//            $deal = explode(' ',$token);
//            $token = $deal[1];
//
//            $uid=$this->lenovo_getuid($token);
//        }
//
//        if (!empty($uid)) {
//            $where = ['id'=>$uid];
//        }

        $view_num = Db::name('qview')->where(['date_time'=>$date_time,'qid'=>$qid])->value('view');

        if (!$view_num) {

            $res = Db::name('qview')
                ->insert(['date_time'=>$date_time,'qid'=>$qid,'view'=>1]);

        }else{
            $res = Db::name('qview')
                ->where(['date_time'=>$date_time,'qid'=>$qid])
                ->update(['view'=>$view_num+1]);
//            var_dump(Db::name('qview')->getLastSql());die;
        }


        if ($res) {
            return $this->output_success(10010,'','添加浏览成功');
//            无返回
        } else {
            return $this->output_error(10000,'添加浏览失败');
        }
    }

    public function unread () {

        $token = !empty($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        if (!is_null($token)){
            $deal = explode(' ',$token);
            $token = $deal[1];
            $uid=$this->lenovo_getuid($token);

        } else {
            $uid = 0;
            $this->output_error(10000,'查询用户失败');
        }


        $res = Db::name('question')->where(['user_id'=>$uid,'status'=>1])->count('status');

        if ($res) {
            return $this->output_success(10010,$res,'查询未阅读成功');
        } else {
            return $this->output_success(10000,0,'查询未阅读为0');
        }
    }
}