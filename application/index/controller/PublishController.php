<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 17:21
 */

namespace app\index\controller;


use think\Db;
use think\Request;

class PublishController extends Base
{

    public function publish()
    {
        //从http头里取token
        $request = Request::instance()->header();
        if (!array_key_exists('authorization',$request)) {
            return $this->output_error(400,'请把token放在http请求头里面');
        }
        $token = explode(' ',$request['authorization']);
        $token = $token[1];
        $uid = $this->lenovo_getuid($token);

        //获取问题
        $question = input('question','','trim');
        $res = Db::name('question')->insert([
            'user_id'=>$uid,
            'question'=>$question,
            'tag_id'=>1
        ]);

        if (!$res) {
            return $this->output_error(404,'发送失败');
        }else{
            return $this->output_success(200,[],'发送成功');
        }
    }

}