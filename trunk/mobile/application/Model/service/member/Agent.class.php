<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Agent.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Agent_mobile extends service_member_Agent_base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getAgentMemberInfo()
    {
        $member_info = parent::getAgentMemberInfo();
        if ( $member_info ) {
            $member_info->member_image_id = $this->getImage( $member_info->member_image_id, '110', 'avatar' );
        }
        return $member_info;
    }

    public function getAgentAll( $uid )
    {
        $dao = dao_factory_base::getMemberDao();
        $where = 'agent_uid=' . $uid;
        $dao->setWhere( $where );
        $dao->setOrderby('uid DESC');
        $res = $dao->getListByWhere();
        if ( $res ) {
            $member_level_array = Tmac::config( 'goods.goods.goods_member_level', APP_BASE_NAME );
            $member_sex_array = Tmac::config( 'member.member.sex', APP_BASE_NAME );
            foreach ( $res as $value ) {
                $value->reg_time = date( 'Y-m-d H:i:s', $value->reg_time );                
                $value->member_image_id = empty( $value->member_image_id ) ? '' : $this->getImage( $value->member_image_id, 110, 'avatar' );
                $value->sex = empty( $member_sex_array[ $value->sex ] ) ? '' : $member_sex_array[ $value->sex ];
                $value->member_level = empty( $member_level_array[ $value->member_level ] ) ? '' : $member_level_array[ $value->member_level ];
                $value->address = unserialize($value->address_info);
            }
        }
        return $res;
    }

}
