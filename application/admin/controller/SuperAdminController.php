<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/3/2
 * Time: 10:28
 */

namespace app\admin\controller;


use think\Db;


class SuperAdminController extends AdminBase
{


    /**
     * 管理员列表
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        $this->check_admin(1);

        $res = Db::name('admin')->field('id,account,password,head_img,info,name,status')->where('is_deleted',0)->select();

        if ($res) {
           return $this->output_success(200,$res,'管理员列表获取成功');
        }else {
           return $this->output_error(400,'管理员列表获取失败');
        }
    }

    /**
     * 新增管理员接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function add_admin()
    {

        $this->check_admin(1);

        $account = input('account','','trim');
        $password = input('password','','trim');
        $name = input('name','','trim');
        $head_img = input('head_img','','trim');
        $info = input('info','','trim');

        if (!$account) {
            return $this->output_error(400,'请输入账号');
        }
        if (!$password) {
            return $this->output_error(400,'请输入密码');
        }

        $res = Db::name('admin')->insert([
            'account'=>$account,
            'password'=>$password,
            'name'=>$name,
            'head_img'=>$head_img,
            'info'=>$info,
            'update_time' => date('Y-m-d H:i:s',time())
        ]);

        if ($res) {
            return $this->output_success(200,[],'管理员新增成功');
        }else{
            return $this->output_error(400,'管理员新增失败');
        }
    }

    /**
     * 上传图片接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function uploads()
    {
        $this->check_admin(1);

        $head_img = request()->file('head_img');

        if (empty($head_img)) {
            return $this->output_error(400,'请上传图片');
        }

        $res = $head_img->move(ROOT_PATH.'public'.DS.'head');
        if ($res) {
            $head_url = 'http://lenovo.weinuoabc.com/head/'.$res->getSaveName();
            return $this->output_success(200,$head_url,'data里面就是照片的url');
        }else {
            return $this->output_error(400,'上传图片失败');
        }
    }

    /**
     * 删除管理员接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delete_admin()
    {

        $this->check_admin(1);
        $admin_id = input('admin_id',0,'intval');
        $res = Db::name('admin')->where('id',$admin_id)->update([
            'is_deleted'=>1,
            'update_time' => date('Y-m-d H:i:s',time())
            ]);
        if ($res) {
            return $this->output_success(200,'管理员删除成功');
        }else {
            return $this->output_error(400,'管理员删除失败');
        }
    }

    /**
     * 冻结管理员接口
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function freeze_admin()
    {

        $this->check_admin(1);
        $admin_id = input('admin_id',0,'intval');
        $res = Db::name('admin')->where('id',$admin_id)->update(['status'=>2]);
        if ($res) {
            return $this->output_success(200,'管理员冻结成功');
        }else {
            return $this->output_error(400,'管理员冻结失败');
        }
    }

    /**
     * 解冻管理员
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function thaw_admin()
    {

        $this->check_admin(1);
        $admin_id = input('admin_id',0,'intval');
        $res = Db::name('admin')->where('id',$admin_id)->update([
            'status'=>1,
            'update_time' => date('Y-m-d H:i:s',time())
            ]);
        if ($res) {
            return $this->output_success(200,[],'管理员解冻成功');
        }else {
            return $this->output_error(400,'管理员解冻失败');
        }
    }

    /**
     * 更新管理员信息
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function update_admin()
    {
        $this->check_admin(1);

        $admin_id = input('admin_id',0,'intval');
        $account = input('account','','trim');
        $password = input('password','','trim');
        $name = input('name','','trim');
        $head_img = input('head_img','','trim');
        $info = input('info','','trim');

        if (!$account) {
            return $this->output_error(400,'请输入账号');
        }
        if (!$password) {
            return $this->output_error(400,'请输入密码');
        }

        $res = Db::name('admin')->where('id',$admin_id)->update([
            'account'=>$account,
            'password'=>$password,
            'name'=>$name,
            'head_img'=>$head_img,
            'info'=>$info,
            'update_time' => date('Y-m-d H:i:s',time())
        ]);

        if ($res) {
            return $this->output_success(200,[],'管理员更新成功');
        }else{
            return $this->output_error(400,'管理员更新失败');
        }

    }
}