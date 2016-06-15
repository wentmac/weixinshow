<?php

/*
 * 加班补给排行榜活动 配置文件
 */

$config['article_status'][1] = '已发表';
$config['article_status'][2] = '草稿';
$config['article_status'][3] = '私人内容(不会被公开)';

$config['comment_status'][0] = '允许评论';
$config['comment_status'][-1] = '不允许评论';
$config['comment_status'][1] = '只有注册用户方可评论';

$config['do_index'][] = '＝批量管理操作＝';
$config['do_index']['del'] = '删除';

$config['do_order'][] = '＝批量管理操作＝';
$config['do_order']['refund'] = '退款';

$config['do'][] = '＝批量管理操作＝';
$config['do']['del'] = '删除';

$config['article']['boolean'][0] = '否';
$config['article']['boolean'][1] = '是';

$config['star'] = array(
    1 => '一星级',
    2 => '二星级',
    3 => '三星级',
    4 => '四星级',
    5 => '五星级' 
);

$config['location_id'] = array(
    1 => '亚龙湾',
    2 => '海棠湾',
    3 => '三亚湾',
    4 => '大东海',
    5 => '三亚及周边',
    6 => '海口及周边'
);

$config['index_show'] = array(
    0 => '不显示',
    1 => '显示'    
);

$config['status']['0'] = '已发表';
$config['status']['1'] = '删除';