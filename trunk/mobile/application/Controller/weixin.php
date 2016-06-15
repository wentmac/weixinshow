<?php

/**
 * 用户登录注册页面
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class weixinAction extends service_Controller_mobile
{

    const token = 'lancepan';

    //定义初始化变量

    public function _init()
    {
        parent::__construct();
    }

    /**
     * 第三方联合登录回调
     * 微博登录
     * TODO将来上线后做
     */
    public function receive()
    {
        $this->valid();
    }

    private function valid()
    {
        $echoStr = empty( $_GET[ "echostr" ] ) ? '' : $_GET[ "echostr" ];

        //valid signature , option
        if ( $this->checkSignature() ) {
            echo $echoStr;
        }
        $this->responseMsg();
    }

    private function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS[ "HTTP_RAW_POST_DATA" ];

        //extract post data
        if ( !empty( $postStr ) ) {

            $postObj = simplexml_load_string( $postStr, 'SimpleXMLElement', LIBXML_NOCDATA );
            //post_error
            Log::getInstance( 'post_error' )->write( var_export( $postObj, true ) );
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = isset( $postObj->Content ) ? trim( $postObj->Content ) : '';
            $event = $postObj->Event;
            $eventKey = $postObj->EventKey;
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if ( !empty( $keyword ) ) {
                $msgType = "text";
                $contentStr = "欢迎来到银品惠";
                $resultStr = sprintf( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
            } else if ( $event == 'subscribe' ) {
                $register_model = new service_account_Register_mobile();
                $register_model->setOpenid( $fromUsername );
                $register_model->setEventKey( $eventKey );
                try {
                    $res = $register_model->mpRegister();
                } catch (TmacClassException $exc) {
                    Log::getInstance( 'post_error' )->write( $exc->getMessage() );
                }
                //$picUrl = STATIC_URL . APP_MOBILE_NAME . 'default/image/1519748916.jpg';
                $newsContent[ 0 ] = array(
                    'title' => '兑换商品请点击',
                    'description' => '恭喜您首次关注获得10积分',
                    'picUrl' => 'http://public.yph.weixinshow.com/mobile/default/image/20160610.jpg',
                    'url' => 'http://yph.weixinshow.com/article/169.html'
                );
                $newsContent[ 1 ] = array(
                    'title' => '一元夺宝',
                    'description' => '参加一元夺宝计划',
                    'picUrl' => 'http://public.yph.weixinshow.com/mobile/default/image/1519748916.jpg',
                    'url' => 'http://www.xinwuwu.com/app/index.php?i=366&c=entry&do=attention&m=feng_duobao&from=singlemessage&isappinstalled=0'
                );
                /**
                  $newsContent[ 1 ] = array(
                  'title' => '平江路',
                  'description' => '平江路位于苏州古城东北，是一条傍河的小路，北接拙政园，南眺双塔，全长1606米，是苏州一条历史攸久的经典水巷。宋元时候苏州又名平江，以此名路...',
                  'picUrl' => 'http://joythink.duapp.com/images/suzhouScenic/pingjianglu.jpg',
                  'url' => 'http://mp.weixin.qq.com/mp/appmsg/show?__biz=MjM5NDM0NTEyMg==&appmsgid=10000056&itemidx=1&sign=ef18a26ce78c247f3071fb553484d97a#wechat_redirect'
                  );* */
                $resultStr = $this->responseMultiNews( $postObj, $newsContent );
                echo $resultStr;
            } else {
                $msgType = "text";
                $contentStr = "欢迎来到银品惠";
                $resultStr = sprintf( $textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr );
            }
            echo $resultStr;
        } else {
            echo "";
            exit;
        }
    }

    private function responseMultiNews( $postObj, $newsContent )
    {
        $bodyCount = count( $newsContent );
        $bodyCount = $bodyCount < 10 ? $bodyCount : 10;


        $newsTplHead = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>{$bodyCount}</ArticleCount>
                <Articles>";
        $newsTplBody = "<item>
                <Title><![CDATA[%s]]></Title> 
                <Description><![CDATA[%s]]></Description>
                <PicUrl><![CDATA[%s]]></PicUrl>
                <Url><![CDATA[%s]]></Url>
                </item>";
        $newsTplFoot = "</Articles>                
                </xml>";
        $header = sprintf( $newsTplHead, $postObj->FromUserName, $postObj->ToUserName, time() );

        foreach ( $newsContent as $value ) {
            $body .= sprintf( $newsTplBody, $value[ 'title' ], $value[ 'description' ], $value[ 'picUrl' ], $value[ 'url' ] );
        }

        $FuncFlag = 0;
        $footer = sprintf( $newsTplFoot, $FuncFlag );
        return $header . $body . $footer;
    }

    private function checkSignature()
    {
        $signature = empty( $_GET[ "signature" ] ) ? '' : $_GET[ "signature" ];
        $timestamp = empty( $_GET[ "timestamp" ] ) ? '' : $_GET[ "timestamp" ];
        $nonce = empty( $_GET[ "nonce" ] ) ? '' : $_GET[ "nonce" ];

        $token = self::token;
        $tmpArr = array( $token, $timestamp, $nonce );
        sort( $tmpArr );
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if ( $tmpStr == $signature ) {
            return true;
        } else {
            return false;
        }
    }

    public function test()
    {
        $postObj = array(
            'ToUserName' => 'gh_4eccc030a8c9',
            'FromUserName' => 'omNdGt2aXTLCxSWogEba67qFtppE',
            'CreateTime' => '1457352444',
            'MsgType' => 'event',
            'Event' => 'subscribe',
            'EventKey' => 'qrscene_37',
            'Ticket' => 'gQGU8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0FrenlKUVhsZ3NIN01fbUFEbUpjAAIEhm7dVgMEgDoJAA==',
        );
        //post_error        
        $fromUsername = $postObj[ 'FromUserName' ];
        $toUsername = $postObj[ 'ToUserName' ];
        $event = $postObj[ 'Event' ];
        $eventKey = $postObj[ 'EventKey' ];
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        if ( $event === 'subscribe' ) {
            $register_model = new service_account_Register_mobile();
            $register_model->setOpenid( $fromUsername );
            $register_model->setEventKey( $eventKey );

            try {
                $register_model->mpRegister();
            } catch (TmacClassException $exc) {
                die( $exc->getMessage() );
            }
        } else {
            echo "";
        }
    }

}
