<?php
/**
 * Created by PhpStorm.
 * User: liu
 * Date: 2019/2/12
 * Time: 13:29
 */
namespace app\admin\controller;


use redis\Redis;
use think\Controller;
use think\Config;
use think\Db;
use think\Request;
use token\Token;
class AdminBase extends Controller
{
    /**
     * 设置请求头
     */
    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Methods", "POST, PUT, OPTIONS");
        header('Content-Type:application/json; charset=utf-8');
    }

    /**
     * 空方法处理
     * @return
     */
    public function _empty()
    {
        return $this->output_error(10001, '请求不存在');
    }

    /**
     * 成功消息输出
     * @param $code
     * @param array $data
     * @param string $msg
     * @return array
     */
    public function output_success($code, $data = array(), $msg = '')
    {
        $json = [
            'status' => 1,
            'code' => $code,
            'data' => $data,
            'msg' => $msg,
        ];

        return $json;
    }

    /**
     * 失败消息输出
     * @param $code
     * @param string $msg
     * @return array
     */
    public function output_error($code, $msg = '')
    {
        $json = [
            'status' => 0,
            'code' => $code,
            'msg' => $msg,
        ];
        return $json;
    }


    /**
     * 创建token
     * @param $admin_id
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function token_create($admin_id){


        //创建token=sha1(user_id + secret_key+salt+time())
        $token=sha1($admin_id.time().rand(233,1232));

        //存储token
        Db::name('admin')->where('admin_id',$admin_id)->update(['token'=>$token,'expire'=>time()]);
        //返回token
        return $token;
    }

    /**
     * 判断管理员是否登陆
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function check_admin($sup_admin = null)
    {
        //从http头里取token
        $request = Request::instance()->header();
        if (!array_key_exists('authorization',$request)) {
            $json = $this->output_error(400,'请把token放在http请求头里面');
            echo json_encode($json,JSON_UNESCAPED_UNICODE);exit;
        }


        //检查登陆
        $token = explode(' ',$request['authorization']);
        if (!array_key_exists('1',$token)) {
            header('HTTP/1.0 401 Unauthorized');exit;
//            $json = $this->output_error(401,'请登陆');
//            echo json_encode($json,JSON_UNESCAPED_UNICODE);exit;
        }

        //判断这个管理员有没有被删除
        $token =  $token[1];
        $del = Db::name('admin')->where('token',$token)->value('is_deleted');
        if ($del) {
            $json = $this->output_error(500,'你管理员身份已经被解除，请离开');
            echo json_encode($json,JSON_UNESCAPED_UNICODE);
        }


        if ($sup_admin) {
            $res = Db::name('admin')->where('token',$token)->value('status');
            if (!$res == 1) {
                $json = $this->output_success(401,[],'你不是管理员');
                echo json_encode($json);
            }

        }


        //判断是否超时
        $timestamp = Db::name('admin')->where(['token'=>$token])->value('expire');
        if (!$timestamp) {
            $json = $this->output_error(400,'这是你伪造的token，你的ip是'.$_SERVER["REMOTE_ADDR"].'已经交由警方处理');
            echo json_encode($json,JSON_UNESCAPED_UNICODE);exit;
        }
        $timediff = (int)$timestamp-time();
        $days = intval($timediff/86400);
        if ($days >= 30) {
            $json = $this->output_error(401,'登陆超时');
            echo json_encode($json,JSON_UNESCAPED_UNICODE);exit;
        }else {
            Db::name('admin')->where('token',$token)->update(['expire'=>time()]);
        }
        return Db::name('admin')->where('token',$token)->value('id');
    }




//    前端可封装
    //发送请求
//    function request(ajax, sign) {
////        if (sign != undefined) {
////            if (ajax.data == undefined) ajax.data = {};
////        ajax.data.timestamp = ((new Date()).getTime()) / 1000;
////        ajax.data.token = token;
////        ajax.data.sign = hex_sha1(token + salt + ajax.url.toLowerCase() + ajax.data.timestamp);
////    }
////        $.ajax(ajax);
////    }

    /**
     * 检查sign是否正常
     * @return mixed
     */
    protected function check_sign()
    {
        $token = input('param.token');
        $sign = input('param.sign');
        $timestamp = input('param.timestamp');
        //1. 验证参数是否为空
        //===============
        if (empty($token)) {
            $json = $this->output_error(10005, 'token不能为空');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (empty($sign)) {
            $json = $this->output_error(10006, '签名不能为空');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (empty($timestamp)) {
            $json = $this->output_error(10007, '时间戳不能为空');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }

        //2. 验证时间戳是否超时
        //===============
        //2.1 验证请求时间与服务器时间的误差
        $time_now = time();
        $min_time = $time_now - (Config::get('request.over_expire_time')) / 2;
        $max_time = $time_now + (Config::get('request.over_expire_time')) / 2;
        //时间不合法
        if ($timestamp > $max_time || $timestamp < $min_time) {
            $json = $this->output_error(10008, '时间戳错误');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
        //2.2 验证登录状态是否过期
        $redis = Redis::getRedis();
        $token_server_info = $redis->hGetAll('uid_' . $token);
        if ($token_server_info) {
            if (($token_server_info['update_time'] + Config::get('token.expire_time')) < $time_now) {
                $json = $this->output_error(10010, '登录过期');
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            $json = $this->output_error(10010, '登录过期');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }


        //3. 验证sign是否合法
        //==============
        //3.1 获取sign_server
        $module = strtolower(Request::instance()->module());
        $controller = strtolower(Request::instance()->controller());
        $action = strtolower(Request::instance()->action());
        $request_uri = '/' . $module . '/' . $controller . '/' . $action;
        $salt = $token_server_info['salt'];
        $sign_server = sha1($token . $salt . $request_uri . $timestamp);

        //3.2 查询是否存在相同的sign
        //签名sign过期时间应该等于token过期时间，保证在token生命周期内不会有重复的sign
        $redis = Redis::getRedis();
        $sign_exist = $redis->get('sign_' . $sign);
        if ($sign_exist) {
            $json = $this->output_error(10009, '签名异常');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }

        //3.3 对比客户端和服务器端签名是否一致
        if ($sign === $sign_server) {
            //存储sign,方便3.2验证
            $redis->setex('sign_' . $sign, Config::get('token.expire_time'), 1);
            return $token;
        } else {

            var_dump($request_uri);
            trace('debug--'.$token .'|'. $salt .'|' . $request_uri  .'|'. $timestamp .'|'.$sign, 'debug');
            $json = $this->output_error(10004, '请求认证失败');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }


    /**
     * 检查sign是否正常
     * @return mixed
     */
    protected function admin_check_sign()
    {
        $token = input('param.token');
        $sign = input('param.sign');
        $timestamp = input('param.timestamp');
        //1. 验证参数是否为空
        //===============
        if (empty($token)) {
            $json = $this->output_error(10005, 'token不能为空');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (empty($sign)) {
            $json = $this->output_error(10006, '签名不能为空');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (empty($timestamp)) {
            $json = $this->output_error(10007, '时间戳不能为空');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }

        //2. 验证时间戳是否超时
        //===============
        //2.1 验证请求时间与服务器时间的误差
        $time_now = time();
        $min_time = $time_now - (Config::get('request.over_expire_time')) / 2;
        $max_time = $time_now + (Config::get('request.over_expire_time')) / 2;
        //时间不合法
        if ($timestamp > $max_time || $timestamp < $min_time) {
            $json = $this->output_error(10008, '时间戳错误');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
        //2.2 验证登录状态是否过期
        $redis = Redis::getRedis();
        $token_server_info = $redis->hGetAll('uid_' . $token);
        if ($token_server_info) {
            if (($token_server_info['update_time'] + Config::get('token.expire_time')) < $time_now) {
                $json = $this->output_error(10010, '登录过期');
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            $json = $this->output_error(10010, '登录过期');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }


        //3. 验证sign是否合法
        //==============
        //3.1 获取sign_server
        $module = strtolower(Request::instance()->module());
        $controller = strtolower(Request::instance()->controller());
        $action = strtolower(Request::instance()->action());
        $request_uri = '/' . $module . '/' . $controller . '/' . $action;
        $salt = $token_server_info['salt'];
        $sign_server = sha1($token . $salt . $request_uri . $timestamp);

        //3.2 查询是否存在相同的sign
        //签名sign过期时间应该等于token过期时间，保证在token生命周期内不会有重复的sign
        $redis = Redis::getRedis();
        $sign_exist = $redis->get('sign_' . $sign);
        if ($sign_exist) {
            $json = $this->output_error(10009, '签名异常');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }

        //3.3 对比客户端和服务器端签名是否一致
        if ($sign === $sign_server) {
            //存储sign,方便3.2验证
            $redis->setex('sign_' . $sign, Config::get('token.expire_time'), 1);
            return $token;
        } else {

            var_dump($request_uri);
            trace('debug--'.$token .'|'. $salt .'|' . $request_uri  .'|'. $timestamp .'|'.$sign, 'debug');
            $json = $this->output_error(10004, '请求认证失败');
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }


    protected function get_phone($isSign = true){
        if ($isSign){
            $uid=$this->getuid($isSign);
            $phone=Db::name('user')->where('id',$uid)->value('mobile');
            return $phone;
        }
    }

    protected function upload_file($file) {
        //        $file = request()->file('file');
        $info = $file->move('../../../public/upload');
        if (!$info) {
            return $this->output_error(10010,'上传图片失败');
        } else {
            $path = $info->getSaveName();
            $url = cmf_get_image_preview_url($path);
            return $url;
        }

    }
}