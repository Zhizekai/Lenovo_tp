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

        if ($tag_id == 0) {
            return $this->output_error(10009,'标签id不能为空哦！已经报警了！');
        }

        if ($qid == 0) {
            return $this->output_error(10009,'问题id不能为空哦！已经报警了！');
        }

        $add = Db::name('question')
            ->where(['id'=>$qid])
            ->update(['tag_id'=>$tag_id]);

        if ($add) {
            return $this->output_success(10010,$add,'标签问题操作成功');
        } else {
            return $this->output_success(10000,0,'标签问题操作失败');
        }

    }


    /**
     * 新增标签类别
     * @return array
     */

    public function insert_tag () {

        $this->check_admin(1);

        $tag = input('tag','','trim');

        if (empty($tag)) {
            return $this->output_error(10009,'标签内容不能为空哦！已经报警了！');
        }

        $add = Db::name('tags')->insert(['tag'=>$tag]);

        if ($add) {
            return $this->output_success(10010,$add,'添加新标签类别成功');
        } else {
            return $this->output_success(10000,0,'添加新标签类别失败');
        }

    }

    /**
     * 删除标签类别
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */

    public function delete_tag () {

        $this->check_admin(1);

        $tid = input('tid',0,'intval');

        if ($tid == 0) {
            return $this->output_error(10009,'标签id不能为空哦！已经报警了！');
        }
        $check = Db::name('tags')->alias('t')->join('question q','t.id=q.tag_id')
            ->where('q.tag_id',$tid)
            ->select();
        if ($check) {
            return $this->output_success(10008,[],'该标签类别关联问题无法删除');
        } else {
            $add = Db::name('tags')
                ->where('id',$tid)
                ->delete();
            if ($add) {
                return $this->output_success(10010,$add,'删除标签类别成功');
            } else {
                return $this->output_success(10000,[],'删除标签类别失败');
            }
        }


    }

    /**
     * 更新标签类别
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function up_tag () {

        $this->check_admin(1);

        $tid = input('tid',0,'intval');

        $tag = input('tag','','trim');

        if ($tid == 0) {
            return $this->output_success(10009,[],'标签id不能为空哦！已经报警了！');
        }

        if (empty($tag)) {
            return $this->output_success(10009,[],'标签内容不能为空哦！已经报警了！');
        }

        $add = Db::name('tags')
            ->where('id',$tid)
            ->update(['tag'=>$tag]);

        if ($add) {
            return $this->output_success(10010,$add,'更新新标签类别成功');
        } else {
            return $this->output_success(10000,[],'更新新标签类别失败');
        }

    }

    /**
     * 展示标签
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */

    public function show_tag () {

        $this->check_admin();

        $res = Db::name('tags')->select();

        if ($res) {
            return $this->output_success(10010,$res,'标签提取成功');
        } else {
            return $this->output_success(10000,[],'标签提取失败');
        }

    }

}