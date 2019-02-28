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

            $deal = explode(' ',$token);
            $token = $deal[1];

            $uid=$this->lenovo_getuid($token);

            if (empty($uid)) {
                return $this->output_error(10009,'用户信息错误');
            }
            $where['a.user_id'] = $uid;
            $order = 'status asc';
        }

        $questions = Db::name('question')->alias('a')
            ->join('admin b','a.admin_id=b.id')
            ->where(['b.is_deleted'=>0])
            ->where($where)
            ->order($order)
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
        $status = input('status',1,'intval');
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
}