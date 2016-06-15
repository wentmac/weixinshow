<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Express.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Express_base extends service_Model_base
{

    protected $express_id;
    protected $express_no;
    protected $express_info;
    protected $errorMessage;

    function setExpress_id( $express_id )
    {
        $this->express_id = $express_id;
    }

    function setExpress_no( $express_no )
    {
        $this->express_no = $express_no;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function getExpressInfo()
    {
        $dao = dao_factory_base::getExpressDao();
        $dao->setPk( $this->express_id );
        $express_info = $dao->getInfoByPk();
        if ( !$express_info ) {
            return false;
        }
        $this->express_info = $express_info;
        return $express_info;
    }

    /**
     * 取快递详细
     */
    public function getExpressDetail()
    {
        $kuaidi_key = Tmac::config( 'system.system.kuaidi_key', APP_BASE_NAME );
        $url = 'http://www.aikuaidi.cn/rest/?key=' . $kuaidi_key
                . '&order=' . $this->express_no
                . '&id=' . $this->express_info->express_code
                . '&ord=asc'
                . '&show=json';
        /*
          $url = 'http://api.kuaidi100.com/api?id=' . KUAIDI_KEY . ''
          . '&com=' . $this->express_info->express_code
          . '&nu=' . $this->express_no
          . '&show=0'
          . '&muti=1'
          . '&order=asc';
         * 
         */
        $res = Functions::curl_file_get_contents( $url );
        /*
          $res = '{"message":"ok","status":"1","state":"3","data":
          [{"time":"2012-07-07 13:35:14","context":"客户已签收"},
          {"time":"2012-07-07 09:10:10","context":"离开 [北京石景山营业厅] 派送中，递送员[温]，电话[]"},
          {"time":"2012-07-06 19:46:38","context":"到达 [北京石景山营业厅]"},
          {"time":"2012-07-06 15:22:32","context":"离开 [北京石景山营业厅] 派送中，递送员[温]，电话[]"},
          {"time":"2012-07-06 15:05:00","context":"到达 [北京石景山营业厅]"},
          {"time":"2012-07-06 13:37:52","context":"离开 [北京_同城中转站] 发往 [北京石景山营业厅]"},
          {"time":"2012-07-06 12:54:41","context":"到达 [北京_同城中转站]"},
          {"time":"2012-07-06 11:11:03","context":"离开 [北京运转中心驻站班组] 发往 [北京_同城中转站]"},
          {"time":"2012-07-06 10:43:21","context":"到达 [北京运转中心驻站班组]"},
          {"time":"2012-07-05 21:18:53","context":"离开 [福建_厦门支公司] 发往 [北京运转中心_航空]"},
          {"time":"2012-07-05 20:07:27","context":"已取件，到达 [福建_厦门支公司]"}
          ]} ';
         * 
         */

        $result_array = json_decode( $res );

        $return = '';
        if ( !$result_array ) {
            return $return;
        }

        if ( $result_array->errCode > 0 ) {
            $return = '接口出现异常';
            return $return;
        }
        $weekarray = array( "日", "一", "二", "三", "四", "五", "六" );

        foreach ( $result_array->data as $value ) {
            $time = strtotime( $value->time );
            $date = date( 'Y-m-d', $time );
            $week = $weekarray[ date( 'w', $time ) ];
            $hour_time = date( 'H:i:s', $time );
            $return.='<li><span class="date">' . $date . '</span><span class="week">周' . $week . '</span><span class="time">' . $hour_time . '</span><span class="text">' . $value->content . '</span></li>';
        }

        return $return;
    }

}
