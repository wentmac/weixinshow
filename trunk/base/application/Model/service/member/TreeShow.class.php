<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: TreeShow.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_TreeShow_base extends service_member_Tree_base
{

    private $memberInfoMap = array();

    function getMemberInfoMap()
    {
        return $this->memberInfoMap;
    }

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 打印会员图谱
     */
    public function showAgentRankTree( $root_uid = parent::yph_uid )
    {
//        echo '<pre>';
//        print_r( $this->memberArray );
//        print_r( $this->memberMap );
//        die;
        $res = $this->preMemberTreeTraversal( $root_uid );
        //$this->preMemberTree($root_uid);
        //var_dump( $res );
        //var_dump( $this->parent_uid_array );
        //$this->preMemberTree( $root_uid );
        //var_dump( '三叉树最大级别:' . $this->rank_level );
        //echo 'last_rank_uid:' . $this->last_rank_uid . '<br>';
        //echo 'last_left_rank_uid:' . $this->last_left_rank_uid . '<br>';
        $tree_array = $this->printTreeArray( $root_uid );
        $this->getMemberInfoArray( $tree_array );
        return $tree_array;
    }

    /**
     * 打印出三叉树结构
     */
    private function printTreeArrayB( $root_uid )
    {
        $level = $this->rank_level;
        $tree_array = array();
        $offset = 0;
        for ( $i = 1; $i <= $level; $i++ ) {
            if ( $i == 1 ) {
                $tree_array[ $i ][] = $this->memberArray[ $root_uid ];
            } else {
                $exp = $i - 1;
                $offset += pow( parent::tree_node_count, $exp - 1 );
                $length = pow( parent::tree_node_count, $exp );
                //var_dump( $offset . '|' . $length );
                $this_level_array = array_slice( $this->parent_uid_array, $offset, $length );
                foreach ( $this_level_array as $uid ) {
                    $tree_array[ $i ][] = empty( $this->memberArray[ $uid ] ) ? array() : $this->memberArray[ $uid ];
                }
                //var_dump( $this_level_array );
            }
        }
        echo '<pre>';
        print_r( $tree_array );
    }

    private function getMemberInfoArray( $tree_array )
    {
        $member_id_array = array();
        foreach ( $tree_array as $value ) {
            foreach ( $value as $uuid ) {
                foreach ( $uuid as $id ) {
                    if ( !empty( $id ) ) {
                        $member_id_array[] = $id;
                    }
                }
            }
        }
        $member_id_string = implode( ',', $member_id_array );
        return $this->getMemberInfoArrayByIds( $member_id_string );
    }

    public function getMemberInfoArrayByIds( $member_id_string )
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,username,mobile,realname,nickname,member_image_id,reg_time,sex,member_level' );

        $where = $dao->getWhereInStatement( 'uid', $member_id_string );
        $dao->setWhere( $where );
        $res = $dao->getListByWhere();

        $result = array();
        if ( $res ) {
            $member_level_array = Tmac::config( 'goods.goods.goods_member_level', APP_BASE_NAME );
            $member_sex_array = Tmac::config( 'member.member.sex', APP_BASE_NAME );
            foreach ( $res as $value ) {
                $value->reg_time = date( 'Y-m-d H:i:s', $value->reg_time );
                $value->member_image_id = empty( $value->member_image_id ) ? '' : $this->getImage( $value->member_image_id, 110, 'avatar' );
                $value->sex = empty( $member_sex_array[ $value->sex ] ) ? '' : $member_sex_array[ $value->sex ];
                $value->member_level = empty( $member_level_array[ $value->member_level ] ) ? '' : $member_level_array[ $value->member_level ];
                $result[ $value->uid ] = $value;
            }
        }
        $this->memberInfoMap = $result;
        return $result;
    }

}
