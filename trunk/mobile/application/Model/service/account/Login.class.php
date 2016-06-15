<?php

/**
 * 前台 用户登录注册相关 模块 Model
 * ============================================================================
 * @author zhuqiang by time 2014-07-07 
 */
class service_account_Login_mobile extends service_account_Login_base
{

    private $uid;

    function setUid( $uid )
    {
        $this->uid = $uid;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 说明：检查用户登录信息，返回是否登录成功或错误信息
     * @author 
     * @param array $login_info
     */
    public function doLoginInfo()
    {
        $member_info = $this->getMemberInfoById( $this->uid );

        if ( empty( $member_info ) ) {
            $this->errorMessage = '亲，帐号或密码错误哦!';
            return false;
        }

        if ( $this->_checkDenyUser( $member_info ) === false ) {
            $this->errorMessage = '亲，您的账户被禁用，请联系客服';
            return FALSE;
        }

        if ( $this->expries == 1 ) {
            $expire_day = 30;
        } else {
            $expire_day = 1;
        }

        //更新登录成功后member表的数据
        $this->modifyMemberLoginInfo( $member_info->uid );
        //更新cookie
        $this->isApi || $this->updateMemberCookieCheck( $member_info, $expire_day );
        return $member_info;
    }

    /**
     * 绑定手机号到账号
     */
    public function bindMobile( $memberInfo, $mobile )
    {
        //检测当前会员登录成功
        if ( !empty( $memberInfo->mobile ) ) {
            throw new TmacClassException( '账户已经绑定过手机号了' );
        }
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid' );
        $where = "mobile='{$mobile}'";
        $dao->setWhere( $where );
        $count = $dao->getCountByWhere();
        //验证手机号不存在
        if ( $count > 0 ) {
            throw new TmacClassException( '手机号已经存在其他人的账户了' );
        }
        //执行绑定
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->mobile = $mobile;
        
        $dao->setPk($memberInfo->uid);
        $res = $dao->updateByPk( $entity_Member_base );
        if ( !$res ) {
            throw new TmacClassException( '执行绑定失败' );
        }
        return true;
    }

}
