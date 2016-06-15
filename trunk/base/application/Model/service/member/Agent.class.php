<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Agent.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Agent_base extends service_Model_base
{

    protected $agent_uid;
    protected $errorMessage;

    function setAgent_uid( $agent_uid )
    {
        $this->agent_uid = $agent_uid;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * $this->setUid($uid);
     * $this->getAgentMemberInfo();
     */
    public function getAgentMemberInfo()
    {
        $dao = dao_factory_base::getMemberDao();
        $where = "uid={$this->agent_uid}";
        $dao->setWhere( $where );
        $member_info = $dao->getInfoByWhere();
        return $member_info;
    }

}
