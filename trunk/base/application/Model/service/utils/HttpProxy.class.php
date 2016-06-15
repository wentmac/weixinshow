<?php

/*
 * http代理工具
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: HttpProxy.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of EntityCreate
 *
 * @author Tracy McGrady
 * 
 * demo
  try {
  $http_proxy = new service_utils_HttpProxy_base();
  $res = $http_proxy->get('http://demo.kezhan.zhuna.cn/index.php?m=error.test');
  } catch (Exception $e) {
  echo $e->getMessage();
  }
 * 
 */
class service_utils_HttpProxy_base extends Model
{

    const DAILI_PROXY_URL = 'http://dd.51httpdaili.com/api.asp?dd=501120351995362&tqsl=300&sxa=&sxb=&tta=&ktip=&ports=18186&ports=1998&ports=8080&ports=80&ports=81&ports=3128&ports=9999&qt=1&cf=1';

    private $proxy_ip_array;
    private $error_count = 0;

    public function __construct()
    {
        parent::__construct();
        $this->getProxyIPArray();
    }

    /**
     * 取一批代理IP放在对象中备用
     * @return boolean
     * @throws TmacClassException
     */
    private function getProxyIPArray()
    {
        $proxy_ip_string = Functions::curl_file_get_contents(self::DAILI_PROXY_URL, $timeout = '20');
        if ($proxy_ip_string === false) {
            throw new TmacClassException('获取代理IP的接口出错');
        }
        $this->proxy_ip_array = explode("\n", $proxy_ip_string);
        array_pop($this->proxy_ip_array);
        return true;
    }

    /**
     * 取出对象中的一个代理IP
     * @return type
     */
    private function getProxyIPOne()
    {
        if ($this->proxy_ip_array) {
            return array_shift($this->proxy_ip_array);
        } else {
            $this->getProxyIPArray();
            $this->getProxyIPOne();
        }
    }

    /**
     * curl取文件
     * @param type $url
     * @param type $timeout
     * @param type $ssl
     * @return type 
     */
    private function curl_file_get_contents($url, $timeout = 5, $ssl = false)
    {
        $proxy = $this->getProxyIPOne();
        $rand_ip = $this->getRandIP();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('CLIENT-IP:' . $rand_ip, 'X-FORWARDED-FOR:' . $rand_ip));  //此处可以改为任意假IP
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);        
        curl_close($ch);
        return $r;
    }

    /**
     * 随机取国内的IP
     * @return type
     */
    private function getRandIP()
    {
        $ip_long = array(
            array('607649792', '608174079'), //36.56.0.0-36.63.255.255
            array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
            array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
            array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
            array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
            array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
            array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
            array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
            array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
            array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
        );
        $rand_key = mt_rand(0, 9);
        $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        return $ip;
    }

    public function get($url)
    {
        $result = $this->curl_file_get_contents($url, $timeout = 60);        
        if ($result === false) {
            if ($this->error_count > 100) {
                throw new TmacClassException('用代理IP抓取网页失败次数过多');
            }
            $this->error_count++;
            return $this->get($url);
        } else {
            $this->error_count = 0;
            return $result;
        }
    }

}
