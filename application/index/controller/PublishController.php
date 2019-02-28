<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/2/23
 * Time: 17:21
 */

namespace app\index\controller;


use think\Db;

class PublishController extends Base
{

    public function publish()
    {
        //从http头里取token
        if (!array_key_exists('HTTP_AUTHORIZATION',$_SERVER)) {
            return $this->output_error(400,'请把token放在http请求头里面');
        }
        $token = $_SERVER['HTTP_AUTHORIZATION'];
        $deal = explode(' ',$token);
        $token = $deal[1];

        $uid = $this->lenovo_getuid($token);

        $question = input('question','','trim');






        $res = Db::name('question')->insert([
            'user_id'=>$uid,
            'question'=>$question,
        ]);

        if (!$res) {
            return $this->output_error(404,'发送失败');
        }else{
            return $this->output_success(200,[],'发送成功');
        }
    }

}