<?php

/**
 * WEB后台 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: MemberTemp.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_MemberTemp_crontab extends service_Model_base
{

    private $cookie_jar;

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
        $this->cookie_jar = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'yph_tcpan_com.txt';
    }

    public function getMemberTempArray()
    {
        //登录系统
        $this->login();
//        $memberTempInfo = $this->getMemberTempInfo( 6 );
//        $memberTempInfo = json_decode( $memberTempInfo );
//        var_dump( $memberTempInfo );
//        die;
        $dao = dao_factory_base::getMemberTempDao();

        for ( $i = 1; $i < 7703; $i++ ) {
            $mid = $i;
            $memberTempInfo = $this->getMemberTempInfo( $mid );
            if ( !empty( $memberTempInfo ) ) {
                $memberTempInfo = json_decode( $memberTempInfo );
                $member_check = $this->checkIsExist( $mid );
                if ( $member_check ) {
                    continue;
                }
                $entity_MemberTemp_base = new entity_MemberTemp_base();
                $entity_MemberTemp_base->city = $memberTempInfo->city;
                $entity_MemberTemp_base->province = $memberTempInfo->province;
                $entity_MemberTemp_base->content = $memberTempInfo->content;
                $entity_MemberTemp_base->country = $memberTempInfo->country;
                $entity_MemberTemp_base->email = $memberTempInfo->email;
                $entity_MemberTemp_base->headimgurl = $memberTempInfo->headimgurl;
                $entity_MemberTemp_base->mid = $memberTempInfo->mid;
                $entity_MemberTemp_base->name = $memberTempInfo->name;
                $entity_MemberTemp_base->nickname = $memberTempInfo->nickname;
                $entity_MemberTemp_base->openid = $memberTempInfo->openid;
                $entity_MemberTemp_base->unionid = $memberTempInfo->unionid;
                $entity_MemberTemp_base->phone = $memberTempInfo->phone;
                $entity_MemberTemp_base->tj_mid = $memberTempInfo->tj_mid;
                $entity_MemberTemp_base->type = $memberTempInfo->type;
                $entity_MemberTemp_base->bz = $memberTempInfo->bz;
                $entity_MemberTemp_base->sex = $memberTempInfo->sex;
                $entity_MemberTemp_base->subscribe_time = $memberTempInfo->subscribe_time;
                
                $dao->insert($entity_MemberTemp_base);
            }
        }
    }

    private function checkIsExist( $mid )
    {
        $dao = dao_factory_base::getMemberTempDao();
        $dao->setField( 'uid' );
        $where = "mid={$mid}";
        $dao->setWhere( $where );
        $memberTempInfo = $dao->getInfoByWhere();
        return $memberTempInfo;
    }

    /**
     * 发送短信的API
     * @param type $mobile
     * @param type $message
     * @return type
     */
    private function getMemberTempInfo( $mid )
    {
        $ch = curl_init();
        $url = 'http://yph.tcpan.com/iadmin/member/get_one';
        curl_setopt( $ch, CURLOPT_URL, $url );

        curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->cookie_jar ); // use cookie  

        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );

        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36" );

        /**
         * 不用登录

          $headers = array(
          "X-Requested-With: XMLHttpRequest"
          );
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         */
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, array( 'idx' => $mid ) );

        $res = curl_exec( $ch );
        curl_close( $ch );
        return $res;
    }

    private function login()
    {
        $url = 'http://yph.tcpan.com/iadmin/login/dologin';
        $cookie_jar = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . 'yph_tcpan_com.txt';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );

        //cookie
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookie_jar );
        //curl_setopt( $ch, CURLOPT_COOKIE, $cookie );
        //curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 30 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );

        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.109 Safari/537.36" );


        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, array( 'phone' => '18329070705', 'password' => '123456', 'remember' => '1' ) );


        $res = curl_exec( $ch );
        curl_close( $ch );
        return $res;
    }

}
