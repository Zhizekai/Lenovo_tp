<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/3/3
 * Time: 11:51
 */

namespace app\index\controller;
use DatePeriod;
use DateTime;
use DateInterval;

class IndexController extends Base
{
    public function index()
    {
        $start=new DateTime('2019-5-6');
        $end = date("Y-m-d",time());
        var_dump($end);


    }
    public function get_pv()
    {
        $start_year = input('start_year/s');
        $start_month = input('start_month/s');
        $start_day = input('start_day/s');

        $start=new DateTime($start_year.'-'.$start_month.'-'.$start_day);



        $week = date("Y-m-d",strtotime($start_year.'-'.$start_month.'-'.$start_day." -1 week"));
        $end=new DateTime($week);
        $all_pv = 0;


        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $sb = exec('/root/zzkPv.sh'.' '.$zzk->format('d').' '.$zzk->format('M').' '.$zzk->format('Y'),$pv);
            $all_pv += (int)$sb;
        }
        return $this->output_success(200,$all_pv,'这是一周pv');
    }
}