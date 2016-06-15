<?php
$db['default']['hostname'] = "localhost:3306";    // 数据库主机地址 冒号后面是端口
$db['default']['username'] = "root";    //数据库连接账户名
$db['default']['password'] = "123";    //数据库连接密码
$db['default']['database'] = "tblog";    //数据库名
$db['default']['dbprefix'] = "tb_";     //数据表前缀
$db['default']['char_set'] = "utf8";    //SET NAMES 编码
$db['default']['pconnect'] = FALSE;           //是否打开长连接
$db['default']['dbdriver'] = "MySQL";       //数据库类型 可以为 MySQl  MySQLi
$db['default']['resulttype'] = MYSQL_ASSOC;   //获取结果值的方式
?>