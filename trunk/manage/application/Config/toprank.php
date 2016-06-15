<?php
/*
 * 加班补给排行榜活动 配置文件
 * @param   string  $tr_start    活动开始时间
 * @param   string  $tr_stop     活动结束时间
 * @param   string  $open_day    每周?开奖
 * @param   string  $open_time   开奖时间
 */

$config['toprank']['tr_start'] = '2010-11-15 15:00:00'; //周一
$config['toprank']['tr_stop'] = '2010-12-27 14:59:59';  //周日
$config['toprank']['open_day'] = '1';                   //每周1开奖
$config['toprank']['open_time'] = '13:00';              //开奖时间
$config['toprank']['gift_type'] = '206';                //奖品id
$config['cycle_period']['1'] = array(
	's' =>'2010年11月15日15:00',
	'e' =>'2010年11月29日14:59',
	'sd' =>'2010-11-15',
	'ed' =>'2010-11-29',
);
$config['cycle_period']['2'] = array(
	's' =>'2010年11月29日15:00',
	'e' =>'2010年12月13日14:59',
	'sd' =>'2010-11-29',
	'ed' =>'2010-12-13',
);
$config['cycle_period']['3'] = array(
	's' =>'2010年12月13日15:00',
	'e' =>'2010年12月27日14:59',
	'sd' =>'2010-12-13',
	'ed' =>'2010-12-27',
);
$config['cycle']['one']['start'] = strtotime('2010-11-15 15:00:00');
$config['cycle']['one']['end'] = strtotime('2010-11-29 14:59:59');
$config['cycle']['two']['start'] = strtotime('2010-11-29 15:00:00');
$config['cycle']['two']['end'] = strtotime('2010-12-13 14:59:59');
$config['cycle']['three']['start'] = strtotime('2010-12-13 15:00:00');
$config['cycle']['three']['end'] = strtotime('2010-12-27 14:59:59');
$config['cron_date']['1'] = '11-29';
$config['cron_date']['2'] = '12-13';
$config['cron_date']['3'] = '12-27';
?>
