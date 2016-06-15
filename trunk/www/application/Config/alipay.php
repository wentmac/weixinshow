<?php

/* *
 * 配置文件
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 * 提示：如何获取安全校验码和合作身份者id
 * 1.用您的签约支付宝账号登录支付宝网站(www.alipay.com)
 * 2.点击“商家服务”(https://b.alipay.com/order/myorder.htm)
 * 3.点击“查询合作者身份(pid)”、“查询安全校验码(key)”

 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
 * 解决方法：
 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
 * 2、更换浏览器或电脑，重新登录查询。
 */

//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
//合作身份者id，以2088开头的16位纯数字
$config[ 'alipay_config' ][ 'partner' ] = '2088*********91';

//安全检验码，以数字和字母组成的32位字符
//如果签名方式设置为“MD5”时，请设置该参数
$config[ 'alipay_config' ][ 'key' ] = 'duwc1ti*****m**ryg';

//签约支付宝账号或卖家支付宝帐户
$config[ 'alipay_config' ][ 'seller_email' ] = 'admin@weixinshow.com';


//商户的私钥（后缀是.pem）文件相对路径
//如果签名方式设置为“0001”时，请设置该参数
$config[ 'alipay_config' ][ 'private_key_path' ] = 'key/rsa_private_key.pem';

//支付宝公钥（后缀是.pem）文件相对路径
//如果签名方式设置为“0001”时，请设置该参数
$config[ 'alipay_config' ][ 'ali_public_key_path' ] = 'key/alipay_public_key.pem';

//签名方式 不需修改 strtoupper('MD5')|0001|rsa
$config[ 'alipay_config' ][ 'sign_type' ] = strtoupper( 'MD5' );

//字符编码格式 目前支持 gbk 或 utf-8
$config[ 'alipay_config' ][ 'input_charset' ] = strtolower( 'utf-8' );

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$config[ 'alipay_config' ][ 'transport' ] = 'http';

//页面跳转同步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
//return_url的域名不能写成http://localhost/create_direct_pay_by_user_php_utf8/return_url.php ，否则会导致return_url执行无效
$config[ 'alipay_config' ][ 'return_url' ] = INDEX_URL . 'pay/alipay.returnurl';

//服务器异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
$config[ 'alipay_config' ][ 'notify_url' ] = INDEX_URL . 'pay/alipay.notify';
//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
//服务器退款异步通知页面路径，要用 http://格式的完整路径，不允许加?id=123这类自定义参数 
//退款
$config[ 'alipay_config' ][ 'refund_notify_url' ] = INDEX_URL . 'pay/alipay.autoRefund';

$config[ 'alipay_config' ][ 'format' ] = "xml";
$config[ 'alipay_config' ][ 'v' ] = "2.0";

//验证支付宝账号要用到
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$config[ 'alipay_config' ][ 'cacert' ] = TMAC_BASE_PATH . APP_WWW_NAME . DIRECTORY_SEPARATOR . APPLICATION . '/Plugin/payment/alipay/cacert.pem';
?>