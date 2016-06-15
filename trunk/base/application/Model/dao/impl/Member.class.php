<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: Member.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */

/**
 * Description of article
 *
 * @author Tracy McGrady
 */
class dao_impl_Member_base extends dao_BaseDao_base
{

    protected $url;

    function getUrl()
    {
        return $this->url;
    }

    public function __construct( $link_identifier )
    {
        parent::__construct( $link_identifier );
        $this->table = DB_WS_PREFIX . 'member';
        $this->member_setting_table = DB_WS_PREFIX . 'member_setting';
        $this->setPrimaryKeyField( 'uid' );
    }

    public function getMemberListArray( entity_parameter_Member_admin $entity_parameter_Member_admin )
    {
        $where = "1=1";
        if ( !empty( $entity_parameter_Member_admin->member_type ) ) {
            $where .= " AND a.member_type={$entity_parameter_Member_admin->member_type} ";
        }
        if ( $entity_parameter_Member_admin->member_class <> -1 ) {
            $where .= " AND a.member_class={$entity_parameter_Member_admin->member_class} ";
        }
        if ( $entity_parameter_Member_admin->agent_lock <> 0 ) {
            $agent_lock = $entity_parameter_Member_admin->agent_lock - 1;
            $where .= " AND a.agent_lock={$agent_lock} ";            
        }
        //解析$query_string
        if ( !empty( $entity_parameter_Member_admin->query_string ) ) {
            if ( preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $entity_parameter_Member_admin->query_string ) ) {
                $where .= " AND a.mobile='{$entity_parameter_Member_admin->query_string}'";
            } elseif ( preg_match( '/^[0-9]{2,15}$/u', $entity_parameter_Member_admin->query_string ) ) {
                $where .= " AND a.uid={$entity_parameter_Member_admin->query_string}";
            } else {
                $where .= " AND a.nickname LIKE '%{$entity_parameter_Member_admin->query_string}%'";
            }
        }
        if ( !empty( $entity_parameter_Member_admin->start_date ) ) {
            $where .= " AND a.reg_time>=" . strtotime( $entity_parameter_Member_admin->start_date );
        }
        if ( !empty( $entity_parameter_Member_admin->end_date ) ) {
            $where .= " AND a.reg_time<=" . strtotime( $entity_parameter_Member_admin->end_date );
        }

        $this->setWhere( $where );

        $sql = "SELECT ";
        $sql .= "{$this->getField()} "
                . "FROM {$this->getTable()} a INNER JOIN {$this->member_setting_table} b ON a.uid=b.uid ";
        if ( $this->getWhere() != null ) {
            $sql .= "WHERE {$this->getWhere()} ";
        }
        if ( $this->getOrderby() != null ) {
            $sql .= "ORDER BY {$this->getOrderby()} ";
        }
        if ( $this->getLimit() != null ) {
            $sql .= "LIMIT {$this->getLimit()}";
        }

        $res = $this->getDb()->getAllObject( $sql );
        return $res;
    }

    public function getMemberListCount( entity_parameter_Member_admin $entity_parameter_Member_admin )
    {
        $where = '1=1';
        if ( empty( $entity_parameter_Member_admin->url ) ) {
            $url = PHP_SELF . '?m=member';
        } else {
            $url = $entity_parameter_Member_admin->url;
        }

        if ( !empty( $entity_parameter_Member_admin->member_type ) ) {
            $where .= " AND member_type={$entity_parameter_Member_admin->member_type} ";
            $url .= '&member_type=' . $entity_parameter_Member_admin->member_type;
        }
        if ( $entity_parameter_Member_admin->member_class <> -1 ) {
            $where .= " AND member_class={$entity_parameter_Member_admin->member_class} ";
            $url .= '&member_class=' . $entity_parameter_Member_admin->member_class;
        }
        if ( $entity_parameter_Member_admin->agent_lock <> 0 ) {
            $agent_lock = $entity_parameter_Member_admin->agent_lock - 1;
            $where .= " AND agent_lock={$agent_lock} ";
            $url .= '&agent_lock=' . $entity_parameter_Member_admin->agent_lock;
        }
        //解析$query_string
        if ( !empty( $entity_parameter_Member_admin->query_string ) ) {
            if ( preg_match( '/^1([3]|[5]|[8]|[4]|[7])[0-9]{9}$/', $entity_parameter_Member_admin->query_string ) ) {
                $where .= " AND mobile='{$entity_parameter_Member_admin->query_string}'";
            } elseif ( preg_match( '/^[0-9]{2,15}$/u', $entity_parameter_Member_admin->query_string ) ) {
                $where .= " AND uid={$entity_parameter_Member_admin->query_string}";
            } else {
                $where .= " AND nickname LIKE '%{$entity_parameter_Member_admin->query_string}%'";
            }
            $url .= '&query_string=' . $entity_parameter_Member_admin->query_string;
        }
        if ( !empty( $entity_parameter_Member_admin->start_date ) ) {
            $where .= " AND reg_time>=" . strtotime( $entity_parameter_Member_admin->start_date );
            $url .= '&start_date=' . $entity_parameter_Member_admin->start_date;
        }
        if ( !empty( $entity_parameter_Member_admin->end_date ) ) {
            $where .= " AND reg_time<=" . strtotime( $entity_parameter_Member_admin->end_date );
            $url .= '&end_date=' . $entity_parameter_Member_admin->end_date;
        }

        $url .= '&page=';
        $this->url = $url;
        $this->setWhere( $where );
        $count = $this->getCountByWhere();
        return $count;
    }

}
