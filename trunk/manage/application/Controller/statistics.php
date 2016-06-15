<?php

/**
 * 统计
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class statisticsAction extends service_Controller_manage
{

    //定义初始化变量

    public function __construct()
    {
        parent::__construct();
        $this->checkLogin();
    }

    public function detail()
    {
        $type = Input::get( 'type', '' )->required( '统计类型不能为空' )->string();

        if ( Filter::getStatus() === false ) {
            throw new ApiException( Filter::getFailMessage() ); //会返回上面参数接收时第一个失败的required里的错误内容，或格式失败的内容
        }

        try {
            $model = service_Statistics_base::factory( $type );
            $model->setUid( $this->memberInfo->uid );
            $model->setMemberInfo( $this->memberInfo );
            $week_array = $model->getWeekList();
            /*
              foreach ( $week_array as $key => $value ) {
              $week_array[ $key ][ 'total' ] = rand( 9, 34 );
              }
              $week_array[ 2 ][ 'total' ] = 690.21;
              $week_array[ 4 ][ 'total' ] = '2014.40';
              $week_array[ 7 ][ 'total' ] = '4800.20';
             */

            $history_array = $model->getDetailList();
        } catch (Exception $exc) {
            throw new ApiException( $exc->getMessage() );
        }

        $return = array(
            'week_array' => json_encode( $week_array, true ),
            'history_array' => $history_array,
            'type' => $type
        );

        $this->assign( $return );

//		echo '<pre>';
//		print_r($return);
        $this->V( 'statistics_detail' );
    }

}
