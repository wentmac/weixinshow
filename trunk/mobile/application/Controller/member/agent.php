<?php

/**
 * 账单 
 * ============================================================================
 * @author  by time 22014-07-07
 * 
 */
class agentAction extends service_Controller_mobile
{

    //定义初始化变量

    public function _init()
    {
        $this->checkLogin();
    }

    /**
     * 我的推荐人
     */
    public function detail()
    {
        $model = new service_member_Agent_mobile();
        $model->setAgent_uid( $this->memberInfo->agent_uid );
        $member_agent_info = $model->getAgentMemberInfo();

        $array[ 'member_agent_info' ] = $member_agent_info;

        $this->assign( $array );

        $this->V( 'member/agent_detail' );
    }

    /**
     * 推荐关系层级排位
     */
    public function level()
    {
        $member_tree_show_model = new service_member_TreeShow_base();
        $member_tree_show_model->setRank_level( service_member_TreeShow_base::tree_level_count );
        $tree_array = $member_tree_show_model->showAgentRankTree( $this->memberInfo->uid );
//        echo '<pre>';
//        print_r($tree_array);die;
        $member_info_map = $member_tree_show_model->getMemberInfoMap();
        //取出所有的粉丝
        $member_agent_model = new service_member_Agent_mobile();
        $agent_array = $member_agent_model->getAgentAll( $this->memberInfo->uid );

        $array[ 'tree_array' ] = $tree_array;
        $array[ 'member_info_map' ] = $member_info_map;
        $array[ 'agent_array' ] = $agent_array;
        $array[ 'memberInfo' ] = $this->memberInfo;

        //echo '<Pre>';
        //print_r($array);die;
        $this->assign( $array );

        $this->V( 'member/agent_level' );
    }

    /**
     * 取全部账单列表
     */
    public function get_bill_list()
    {
        $status = Input::get( 'status', '' )->string();
        $pagesize = Input::get( 'pagesize', 10 )->int();

        $order_model = new service_bill_List_manage();
        $order_model->setUid( $this->memberInfo->uid );
        $order_model->setPagesize( $pagesize );
        $order_model->setStatus( $status );

        $order_model->getBillWhere();
        $rs = $order_model->getBillList();
        $this->apiReturn( $rs );
    }

}
