<?php

/**
 * WEB后台 Controller父类 模块 Controller
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: MemberImport.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_MemberImport_crontab extends service_account_Register_base
{

    /**
     * 初始化变量　定义私有变量
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据第一次购买lv1的时间来设置Rank的排位
     */
    public function setMemberAgentRank()
    {
        $member_dao = dao_factory_base::getMemberDao();
        $where = "member_level_time>0 AND agent_rank_uid=0";
        $member_dao->setWhere( $where );
        $member_dao->setOrderby( 'member_level_time ASC' );
        $member_dao->setLimit( 10 );

        //var_dump($member_array);die;
        //$error_array = array();
        //$i=0;
        while ( true ) {
            //$i++;
            $member_array = $member_dao->getListByWhere();
            if ( empty( $member_array ) ) {
                break;
            }
            foreach ( $member_array as $memberInfo ) {
                if ( $memberInfo->uid == service_Member_base::yph_uid ) {
                    continue;
                }
                $member_dao->getDb()->startTrans();
                $member_rank_model = new service_member_Rank_base();
                $member_rank_model->setGoods_member_level( 1 );
                $member_rank_model->import_init( $memberInfo );
                echo $memberInfo->uid . "\n";
                ob_flush();
                if ( $member_dao->getDb()->isSuccess() ) {
                    $member_dao->getDb()->commit();
                    //return true;
                } else {
                    $member_dao->getDb()->rollback();
                    //return false;
                }
            }
        }
    }

    /**
     * 通过老系统中的mid更新新的agent_uid
     */
    private function setAgentUidByMid( $uid, $tj_mid )
    {
        $dao = dao_factory_base::getMemberDao();
        $where = "mid={$tj_mid}";
        $dao->setWhere( $where );
        $member_info = $dao->getInfoByWhere();
        if ( !$member_info ) {
            return true;
        }
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->agent_uid = $member_info->uid;

        $dao->setPk( $uid );
        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * 更新member表中的AgentUid
     */
    public function updateMemberAgentUid()
    {
        $member_temp_dao = dao_factory_base::getMemberDao();
        $member_temp_dao->setOrderby( 'uid ASC' );
        $member_temp_array = $member_temp_dao->getListByWhere();

        $error_array = array();

        foreach ( $member_temp_array as $value ) {
            $register = $this->setAgentUidByMid( $value->uid, $value->tj_mid );
            if ( $register == false ) {
                $error_array[] = $value->mid;
            }
        }
        var_dump( $error_array );
    }

    /**
     * 把member_temp表的数据导到member表中
     */
    public function importMember()
    {
        $member_temp_dao = dao_factory_base::getMemberTempDao();
        $member_temp_dao->setOrderby( 'uid ASC' );
        $member_temp_array = $member_temp_dao->getListByWhere();

        $error_array = array();

        foreach ( $member_temp_array as $value ) {
            $register = $this->importRegister( $value );
            if ( $register == false ) {
                $error_array[] = $value->mid;
            }
        }
        var_dump( $error_array );
    }

    /**
     * 更新excel表中的 会员等级和lv1购买时间。
     */
    public function updateMemberTemp()
    {
        $excel_file = VAR_ROOT . 'Data' . DIRECTORY_SEPARATOR . '1.xls';
        include Tmac::findFile( 'PHPExcel', APP_ADMIN_NAME );
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if ( !$PHPReader->canRead( $excel_file ) ) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if ( !$PHPReader->canRead( $excel_file ) ) {
                echo 'no Excel';
                return true;
            }
        }
        //建立excel对象，此时你即可以通过excel对象读取文件，也可以通过它写入文件
        $PHPExcel = $PHPReader->load( $excel_file );
        /*         * 读取excel文件中的第一个工作表 */
        $currentSheet = $PHPExcel->getSheet( 0 );
        /*         * 取得最大的列号 */
        $allColumn = $currentSheet->getHighestColumn();
        /*         * 取得一共有多少行 */
        $allRow = $currentSheet->getHighestRow();

        $dao = dao_factory_base::getMemberDao();
        $error_array = array();
        //循环读取每个单元格的内容。注意行从1开始，列从A开始
        for ( $rowIndex = 2; $rowIndex <= $allRow; $rowIndex++ ) {
            $member_info = array();
            for ( $colIndex = 'A'; $colIndex <= $allColumn; $colIndex++ ) {
                $addr = $colIndex . $rowIndex;
                $cell = $currentSheet->getCell( $addr )->getFormattedValue();
                if ( $cell instanceof PHPExcel_RichText )     //富文本转换字符串
                    $cell = $cell->__toString();
                //echo $cell . '{|}';
                $member_info[ $colIndex ] = $cell;
            }
            //echo $rowIndex.'<br>';
            $mid = $member_info[ 'A' ];
            $nickname = $member_info[ 'B' ];
            $realname = $member_info[ 'C' ];
            $mobile = $member_info[ 'D' ];
            $current_money = $member_info[ 'E' ];
            $member_level = $member_info[ 'F' ];
            $member_level_time = strtotime( $member_info[ 'G' ] );

            $entity_Member_base = new entity_Member_base();
            $entity_Member_base->member_level_source = $member_level;
            $entity_Member_base->member_level_time = $member_level_time;
            $where = "mid={$mid}";
            $dao->setWhere( $where );
            $res = $dao->updateByWhere( $entity_Member_base );
            if ( !$res ) {
                $error_array[] = $entity_Member_base;
            }
        }
        echo '<pre>';
        print_r( $error_array );
    }

    /**
     * 公众号关注时注册
     * $this->openid;
     * $this->eventKey;
     * $this->mpRegister();
     */
    private function importRegister( $member_source_info )
    {
        //判断是否已经注册过
        $register_status = $this->isOpenidRegister( $member_source_info->openid );
        if ( $register_status == true ) {
            return true;
        }
        $agent_uid = 0;
        $member_image_id = $this->getMemberImageIdFromURL( $member_source_info->headimgurl );
        if ( $member_image_id == false ) {
            $member_image_id = '';
        }
        // 开始存储事务
        // member表
        //开始注册用户        
        $entity_member = new entity_Member_base ();
        //MD5(pass+salt)
        $entity_member->realname = $member_source_info->name;
        $entity_member->nickname = $member_source_info->nickname;
        $entity_member->password = md5( rand( 10000000, 990000000 ) );
        $entity_member->mobile = $member_source_info->phone;
        $entity_member->email = $member_source_info->email;
        $entity_member->member_type = service_Member_base::member_type_buyer;
        $entity_member->member_class = 0;
        $entity_member->member_image_id = $member_image_id;
        $entity_member->reg_time = $this->now;
        $entity_member->salt = rand( 100000, 999999 );
        $entity_member->last_login_time = $this->now;
        $entity_member->last_login_ip = Functions::get_client_ip();
        $entity_member->login_fail_count = 0;
        $entity_member->agent_uid = $agent_uid;
        $entity_member->register_source = service_Account_base::register_source_wechat;
        $entity_member->sex = $member_source_info->sex;
        $address_info = array(
            'country' => $member_source_info->country,
            'province' => $member_source_info->province,
            'city' => $member_source_info->city
        );
        $entity_member->address_info = serialize( $address_info );
        $entity_member->mid = $member_source_info->mid;
        $entity_member->tj_mid = $member_source_info->tj_mid;

        $dao = dao_factory_base::getMemberDao();
        $dao->getDb()->startTrans();

        $uid = $dao->insert( $entity_member );

        $spec_map_dao = dao_factory_base::getSpecMapDao();
        $spec_map_dao->createMemberSpecMap( $uid );

        //会员设置表插入记录
        $member_setting_dao = dao_factory_base::getMemberSettingDao();
        $entity_MemberSetting_base = new entity_MemberSetting_base();
        $entity_MemberSetting_base->uid = $uid;
        $entity_MemberSetting_base->shop_name = '银品惠_' . date( 'YmdHis' ) . rand( 10000, 99999 );
        $entity_MemberSetting_base->member_type = service_Member_base::member_type_buyer;
        $entity_MemberSetting_base->reg_time = $this->now;
        $member_setting_dao->insert( $entity_MemberSetting_base );

        //member_oauth表
        $entity_MemberOauth_base = new entity_MemberOauth_base();
        $entity_MemberOauth_base->uid = $uid;
        $entity_MemberOauth_base->oauth_type = service_Oauth_base::oauth_type_wechat;
        $entity_MemberOauth_base->openid = $member_source_info->openid;
        $entity_MemberOauth_base->unionid = isset( $member_source_info->unionid ) ? $member_source_info->unionid : '';
        $entity_MemberOauth_base->access_token = '';
        $entity_MemberOauth_base->expires_in = 0;
        $entity_MemberOauth_base->refresh_token = '';
        $entity_MemberOauth_base->nickname = $member_source_info->nickname;
        $entity_MemberOauth_base->avatar_imgurl = $member_source_info->headimgurl;
        $entity_MemberOauth_base->oauth_time = $this->now;
        $member_oauth_dao = dao_factory_base::getMemberOauthDao();
        $member_oauth_dao->insert( $entity_MemberOauth_base );

        if ( $dao->getDb()->isSuccess() ) {
            $dao->getDb()->commit();
            $entity_member->uid = $uid;
            return true;
        } else {
            $dao->getDb()->rollback();
            return false;
        }
        //注册的时候判断有没有推荐人,如果有写上
    }

    private function isOpenidRegister( $openid )
    {
        $dao = dao_factory_base::getMemberOauthDao();
        $where = "oauth_type=" . service_Oauth_base::oauth_type_wechat . " AND openid='{$openid}'";
        $dao->setWhere( $where );
        $member_oauth_info = $dao->getInfoByWhere();
        if ( !$member_oauth_info ) {
            return false;
        }
        return true;
    }

}
