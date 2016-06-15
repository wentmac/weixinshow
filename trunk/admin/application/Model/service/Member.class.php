<?php

/**
 * 后台首页小图模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Member.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_Member_admin extends service_Member_base
{

    /**
     * 会员导出
     * 默认｜不导出
     */
    const member_export_default = 0;

    /**
     * 会员导出
     * 导出当前列表数据
     */
    const member_export_current = 1;

    /**
     * 会员导出
     * 导出当前列表所有
     */
    const member_export_all = 2;

    public function __construct()
    {
        parent::__construct();
    }

    public function handelMemberInfo( $memberInfo, $memberSettingInfo )
    {
        $member_type_array = Tmac::config( 'member.member.member_type', APP_BASE_NAME );
        $member_class_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );
        $stock_setting_array = Tmac::config( 'member.member.stock_setting', APP_BASE_NAME );
        $locked_type_array = Tmac::config( 'member.member.locked_type', APP_BASE_NAME );

        $memberInfo->member_image_id = $this->getImage( $memberInfo->member_image_id, '110', 'avatar' );
        $memberInfo->reg_time = date( 'Y-m-d H:i:s', $memberInfo->reg_time );
        $memberInfo->last_login_time = date( 'Y-m-d H:i:s', $memberInfo->last_login_time );
        $memberInfo->member_type_text = $member_type_array[ $memberInfo->member_type ];
        $memberInfo->member_class_text = empty( $member_class_array[ $memberInfo->member_type ][ $memberInfo->member_class ] ) ? '' : $member_class_array[ $memberInfo->member_type ][ $memberInfo->member_class ];


        //取友情操作类型radiobutton数组        
        $memberInfo->member_type_option = Utility::Option( $member_type_array, $memberInfo->member_type );

        //取友情操作类型radiobutton数组        
        $memberInfo->member_class_option = '';
        if ( !empty( $member_class_array[ $memberInfo->member_type ] ) ) {
            $memberInfo->member_class_option = Utility::Option( $member_class_array[ $memberInfo->member_type ], $memberInfo->member_class );
        }


        //取友情操作类型radiobutton数组        
        $memberInfo->locked_type_option = Utility::Option( $locked_type_array, $memberInfo->locked_type );

        $memberSettingInfo->shop_image_id = $this->getImage( $memberSettingInfo->shop_image_id, '110', 'shop' );
        $memberSettingInfo->shop_signboard_image_id = $this->getImage( $memberSettingInfo->shop_signboard_image_id, '110', 'shop' );
        $memberSettingInfo->idcard_positive_image_id = $this->getImage( $memberSettingInfo->idcard_positive_image_id, '200x150', 'idcard' );
        $memberSettingInfo->idcard_negative_image_id = $this->getImage( $memberSettingInfo->idcard_negative_image_id, '200x150', 'idcard' );
        $memberSettingInfo->idcard_image_id = $this->getImage( $memberSettingInfo->idcard_image_id, '200x150', 'idcard' );

        $memberSettingInfo->stock_setting_text = $stock_setting_array[ $memberSettingInfo->stock_setting ];
        $idcard_verify_array = Tmac::config( 'member.member.idcard_verify', APP_BASE_NAME );
        $memberSettingInfo->idcard_verify_option = Utility::OptionObject( $idcard_verify_array, $memberSettingInfo->idcard_verify );


        return array(
            'memberInfo' => $memberInfo,
            'memberSettingInfo' => $memberSettingInfo,
            'member_class_json' => json_encode( $member_class_array, true )
        );
    }

    public function getMemberArray( entity_parameter_Member_admin $entity_parameter_Member_admin )
    {
        $dao = dao_factory_base::getMemberDao();
        $count = $dao->getMemberListCount( $entity_parameter_Member_admin );

        if ( $count === false ) {
            $retmsg = 0;
        } else {
            $retmsg = 1; //业务返回信息
        }
        $pages = $this->P( 'Pages' );
        $pages->setTotal( $count );
        $pages->setUrl( $dao->getUrl() );
        $pages->setPrepage( $entity_parameter_Member_admin->getPagesize() );
        $limit = $pages->getSqlLimit();

        $res = array();
        if ( $count > 0 ) {
            $dao->setOrderby( 'a.uid DESC' );
            $dao->setLimit( $limit );
            $dao->setField( 'a.uid,a.username,a.nickname,a.mobile,a.realname,a.email,a.member_type,a.member_class,a.member_image_id,a.reg_time,a.last_login_time,a.last_login_ip,a.register_source,b.shop_name,b.shop_image_id,b.current_money,b.history_money,a.member_level' );
            $res = $dao->getMemberListArray( $entity_parameter_Member_admin );

            $member_type_array = Tmac::config( 'member.member.member_type', APP_BASE_NAME );
            $member_class_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );
            $member_level_array = Tmac::config( 'goods.goods.goods_member_level', APP_BASE_NAME );
            foreach ( $res as $value ) {
                $value->member_image_id = $this->getImage( $value->member_image_id, '110', 'avatar' );
                $value->reg_time = date( 'Y-m-d H:i:s', $value->reg_time );
                $value->last_login_time = date( 'Y-m-d H:i:s', $value->last_login_time );
                $value->member_type_text = $member_type_array[ $value->member_type ];
                $value->member_class_text = isset( $member_class_array[ $value->member_type ][ $value->member_class ] ) ? $member_class_array[ $value->member_type ][ $value->member_class ] : '';
                $value->member_level = isset( $member_level_array[ $value->member_level ] ) ? $member_level_array[ $value->member_level ] : '不是会员';
            }
        }

        $ErrorMsg = '';
        if ( $count == 0 ) {
            $ErrorMsg = "暂无会员!";
        }

        $result = array(
            'rs' => $res,
            'pageCurrent' => $pages->getNowPage(),
            'page' => $pages->show(),
            'ErrorMsg' => $ErrorMsg
        );
        return $result;
    }

    public function exportMemberArray( entity_parameter_Member_admin $entity_parameter_Member_admin )
    {
        $dao = dao_factory_base::getMemberDao();
        $where = "1=1";

        if ( !empty( $entity_parameter_Member_admin->member_type ) ) {
            $where .= " AND member_type={$entity_parameter_Member_admin->member_type} ";
        }
        if ( $entity_parameter_Member_admin->member_class <> -1 ) {
            $where .= " AND member_class={$entity_parameter_Member_admin->member_class} ";
        }
        //解析$query_string
        if ( !empty( $entity_parameter_Member_admin->query_string ) ) {
            if ( preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $entity_parameter_Member_admin->query_string ) ) {
                $where .= " AND mobile='{$entity_parameter_Member_admin->query_string}'";
            } elseif ( preg_match( '/^[0-9]{2,15}$/u', $entity_parameter_Member_admin->query_string ) ) {
                $where .= " AND uid={$entity_parameter_Member_admin->query_string}";
            }
        }
        if ( !empty( $entity_parameter_Member_admin->start_date ) ) {
            $where .= " AND reg_time>=" . strtotime( $entity_parameter_Member_admin->start_date );
        }
        if ( !empty( $entity_parameter_Member_admin->end_date ) ) {
            $where .= " AND reg_time<=" . strtotime( $entity_parameter_Member_admin->end_date );
        }

        //导出的资源排除自己人的
        $where .= " AND mobile NOT IN('15910986304','13771023935','15601178342','15011420631','13293359887','13016417050','13067870076','13718527728','18871237114','13466612906','18027658642','18372675177','15901484288','18628896185','13071298860','13269561886','13777050761','18622705725','18327611280','18883656968','18507177941','18033522669','13871903123','13135636400','18680910820','18971670906','13733526021','15712137773','13268591918','15900463470','13636074231')";
        $dao->setWhere( $where );
        if ( $entity_parameter_Member_admin->member_export == self::member_export_current ) {
            $count = $dao->getMemberListCount( $entity_parameter_Member_admin );
            $pages = $this->P( 'Pages' );
            $pages->setTotal( $count );
            $pages->setPrepage( $entity_parameter_Member_admin->getPagesize() );
            $limit = $pages->getSqlLimit();
            $dao->setLimit( $limit );
            $dao->setOrderby( 'uid DESC' );
        }

        $res = $dao->getListByWhere();
        $count = count( $res );

        if ( empty( $count ) ) {
            $this->errorMessage = '没有找到数据';
            return false;
        }

        include Tmac::findFile( 'PHPExcel', APP_ADMIN_NAME );
        $objPHPExcel = new PHPExcel();

        $title = "注册用户{$entity_parameter_Member_admin->start_date}至{$entity_parameter_Member_admin->end_date}";
        // Set document properties        
        $objPHPExcel->getProperties()->setCreator( "Maarten Balliauw" )
                ->setLastModifiedBy( "zhangwentao" )
                ->setTitle( $title );
        //设置当前的sheet   
        $objPHPExcel->setActiveSheetIndex( 0 );
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 4, 1, $title );
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth( 10 );
        $xls_header = array( '会员ID', 'username', '手机号码', '用户类型', '用户级别', '用户注册时间', '上次登录时间', '上次登录IP', '推荐人ID' );
        for ( $i = 0; $i < count( $xls_header ); $i++ ) {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( $i, 2, $xls_header[ $i ] );
        }


        $member_type_array = Tmac::config( 'member.member.member_type', APP_BASE_NAME );
        $member_class_array = Tmac::config( 'member.member.member_class', APP_BASE_NAME );

        $row = 3;
        foreach ( $res as $value ) {
            $value->reg_time = date( 'Y-m-d H:i:s', $value->reg_time );
            $value->last_login_time = date( 'Y-m-d H:i:s', $value->last_login_time );
            $value->member_type_text = $member_type_array[ $value->member_type ];
            $value->member_class_text = $member_class_array[ $value->member_type ][ $value->member_class ];

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 0, $row, $value->uid );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 1, $row, $value->username );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 2, $row, $value->mobile );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 3, $row, $value->member_type_text );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 4, $row, $value->member_class_text );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 5, $row, $value->reg_time );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 6, $row, $value->last_login_time );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 7, $row, $value->last_login_ip );
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 8, $row, $value->agent_uid );
            $row++;
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 7, $row, '总数：' );
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( 8, $row, $count );

        // Redirect output to a client’s web browser (Excel2007)
        header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
        header( 'Content-Disposition: attachment;filename=' . $title . '.xlsx' );
        header( 'Cache-Control: max-age=0' );
// If you're serving to IE 9, then the following may be needed
        header( 'Cache-Control: max-age=1' );

// If you're serving to IE over SSL, then the following may be needed
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
        header( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
        header( 'Pragma: public' ); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter( $objPHPExcel, 'Excel2007' );
        $objWriter->save( 'php://output' );
    }

}
