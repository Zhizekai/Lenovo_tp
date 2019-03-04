<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2019/3/2
 * Time: 15:39
 */

namespace app\admin\controller;

use DatePeriod;
use DateTime;
use DateInterval;

class PvController extends AdminBase
{

    public function zzk()
    {
        $start_year = input('start_year/s');
        $start_month = input('start_month/s');
        $start_day = input('start_day/s');

        $start=new DateTime($start_year.'-'.$start_month.'-'.$start_day);



        $week = date("Y-m-d",strtotime($start_year.'-'.$start_month.'-'.$start_day." -1 week"));
        $end=new DateTime($week);



        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $q = $zzk->format('d');
            $w = $zzk->format('M');
            $r = $zzk->format('Y');
            var_dump($q);
            var_dump($w);
            var_dump($r);
        }
        var_dump(2222);

    }

    public function get_all_pv()
    {


        $start=new DateTime('2018-2-4');
        $end=new DateTime(date("Y-m-d",time()));
        $all_pv = 0;


        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $sb = exec('/root/zzkPv.sh'.' '.$zzk->format('d').' '.$zzk->format('M').' '.$zzk->format('Y'),$pv);
            $all_pv += (int)$sb;
        }
        return $this->output_success(200,$all_pv,'这是总的pv');
    }

    public function get_week_pv()
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

    public function get_month_pv()
    {
        $start_year = input('start_year/s');
        $start_month = input('start_month/s');
        $start_day = input('start_day/s');

        $start=new DateTime($start_year.'-'.$start_month.'-'.$start_day);



        $week = date("Y-m-d",strtotime($start_year.'-'.$start_month.'-'.$start_day." -1 month"));
        $end=new DateTime($week);
        $all_pv = 0;


        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $sb = exec('/root/zzkPv.sh'.' '.$zzk->format('d').' '.$zzk->format('M').' '.$zzk->format('Y'),$pv);
            $all_pv += (int)$sb;
        }
        return $this->output_success(200,$all_pv,'这是一个月pv');
    }


    public function get_all_uv()
    {
        $start=new DateTime('2018-2-4');
        $end=new DateTime(date("Y-m-d",time()));
        $all_uv = 0;


        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $sb = exec('/root/zzkUv.sh'.' '.$zzk->format('d').' '.$zzk->format('M').' '.$zzk->format('Y'),$uv);
            $all_uv += (int)$sb;
        }
        return $this->output_success(200,$all_uv,'这是总的pv');
    }

    public function get_week_uv()
    {
        $start_year = input('start_year/s');
        $start_month = input('start_month/s');
        $start_day = input('start_day/s');

        $start=new DateTime($start_year.'-'.$start_month.'-'.$start_day);



        $week = date("Y-m-d",strtotime($start_year.'-'.$start_month.'-'.$start_day." -1 week"));
        $end=new DateTime($week);
        $all_uv = 0;


        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $sb = exec('/root/zzkUv.sh'.' '.$zzk->format('d').' '.$zzk->format('M').' '.$zzk->format('Y'),$uv);
            $all_uv += (int)$sb;
        }
        return $this->output_success(200,$all_uv,'这是一周uv');
    }

    public function get_month_uv()
    {
        $start_year = input('start_year/s');
        $start_month = input('start_month/s');
        $start_day = input('start_day/s');

        $start=new DateTime($start_year.'-'.$start_month.'-'.$start_day);



        $week = date("Y-m-d",strtotime($start_year.'-'.$start_month.'-'.$start_day." -1 month"));
        $end=new DateTime($week);
        $all_uv = 0;


        //从20号到21号是一天
        foreach(new DatePeriod($start,new DateInterval('P1D'),$end) as $zzk) {
            $sb = exec('/root/zzkUv.sh'.' '.$zzk->format('d').' '.$zzk->format('M').' '.$zzk->format('Y'),$uv);
            $all_uv += (int)$sb;
        }
        return $this->output_success(200,$all_uv,'这是一个月uv');
    }
}