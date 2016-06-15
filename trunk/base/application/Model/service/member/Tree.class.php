<?php

/**
 * api 会员账户 管理模块 Model
 * ============================================================================
 * TBlog TBlog博客系统　BY Tmac PHP MVC framework
 * $Author: zhangwentao $  <zwttmac@qq.com>
 * $Id: Tree.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 * http://www.t-mac.org；
 */
class service_member_Tree_base extends service_Member_base
{

    const tree_node_count = 3;

    /**
     * 显示几个层次
     */
    const tree_level_count = 3;

    protected $memberInfo;
    protected $memberArray;
    protected $memberMap;
    protected $last_rank_uid;
    protected $rank_level;
    protected $last_rank_level; //最后一个结点的级别
    protected $left_rank_uid; //新的满三叉树最左边的子结点
    protected $parent_uid_array = array(); //父结点数组。
    protected $parent_top_level = 0;
    protected $root_uid;
    protected $bread_crumb_array;

    function setRank_level( $rank_level )
    {
        $this->rank_level = $rank_level;
    }

    function setMemberInfo( $memberInfo )
    {
        $this->memberInfo = $memberInfo;
    }

    public function __construct()
    {
        parent::__construct();
        $this->getAllVipMember();
    }

    protected function getAllVipMember()
    {
        $dao = dao_factory_base::getMemberDao();
        $dao->setField( 'uid,agent_rank_uid' );
        $where = "agent_lock=" . service_Member_base::agent_lock_yes;
        $dao->setWhere( $where );
        $dao->setOrderby( 'member_level_time ASC' );
        $res = $dao->getListByWhere();
        $result = $member_map = array();
        foreach ( $res as $value ) {
            $result[ $value->agent_rank_uid ][] = $value->uid;
            $member_map[ $value->uid ] = $value->agent_rank_uid;
        }
        $this->memberArray = $result;
        $this->memberMap = $member_map;
        if ( empty( $this->memberArray[ service_Member_base::yph_uid ] ) ) {
            $this->memberArray[ service_Member_base::yph_uid ] = array();
        }
        $this->memberMap[ service_Member_base::yph_uid ] = 0;
        return $result;
    }

    /**
     * 取父级数组
     */
    public function getBreadCrumbArray( $uid )
    {
        $this->getBreadCrumbRecursion( $uid );
        if ( $this->bread_crumb_array ) {
            krsort( $this->bread_crumb_array );
        }
        return $this->bread_crumb_array;
    }

    /**
     * 递归取数据
     */
    private function getBreadCrumbRecursion( $uid )
    {
        if ( !empty( $this->memberMap[ $uid ] ) ) {
            $this->bread_crumb_array[] = $this->memberMap[ $uid ];
            $this->getBreadCrumbRecursion( $this->memberMap[ $uid ] );
        }
        return true;
    }

    /**
     * 更新排位赛佣金
     * 比如.我买了5级会员.我上面第5层的人如果5级就给他排位佣金 对吧
     * 这块限制的就是 .必须是我上面的第几层
     * 错过不补
     * 恩.我买第几级的会员.我上面第几层的 人得钱
     * 不过那个人级数必须大于等于 你所买的级数
     */
    public function getCommissionFeeRank( $member_level )
    {
        //找出我上边的第几级
        $top_level_uid = $this->getParentRankTopLevel( $this->memberInfo->uid, $member_level );
        if ( !empty( $top_level_uid ) ) {
            //找到.判断是不是大于等于第几级会员
            $top_level_member_info = parent::getMemberInfoByUid( $top_level_uid );
            if ( $top_level_member_info->member_level >= $member_level ) {
                //如果是 就给钱. 
                return $top_level_uid;
            }
            return false;
        } else {
            //没有找到.不给了
            return false;
        }
    }

    protected function getParentRankTopLevel( $uid, $member_level )
    {
        if ( $member_level == $this->parent_top_level ) {
            return $uid;
        }
        if ( !empty( $this->memberMap[ $uid ] ) ) {
            $this->parent_top_level++;
            return $this->getParentRankTopLevel( $this->memberMap[ $uid ], $member_level );
        }
    }

    /**
     * 打印会员图谱
     */
    public function modifyAgentRankUid( $root_uid = parent::yph_uid )
    {
        $this->root_uid = $root_uid;
        //echo '<pre>';
        //print_r( $this->memberArray );
        //print_r( $this->memberMap );
        //die;
        //$this->preMemberTree($root_uid);
        //var_dump( $res );
        //$this->preMemberTree( $root_uid );
        //echo 'last_rank_uid:' . $this->last_rank_uid . '<br>';
        //echo 'last_left_rank_uid:' . $this->last_left_rank_uid . '<br>';
        //$this->getMemberMaxTreeDepth( $root_uid );
        //var_dump( '三叉树最大级别:' . $this->rank_level );
        //echo '新的满三叉树最左边的子结点|left_rank_uid:' . $this->left_rank_uid . '<br>';
        //取当前最后结点的级别
        //$this->getMemberLastRankUidTreeDepth( $root_uid, $this->last_rank_uid );
        //echo 'last_rank_level:' . $this->last_rank_level . '<br>';
        //$start = memory_get_usage();
        $res = $this->preMemberTreeTraversal( $root_uid );
        $tree_array = $this->getFirstEmptyTreeArray( $root_uid );
        $final_parent_uid = $this->getSystemFinalParentUid( $tree_array );
        //$end = memory_get_usage();
        //echo 'argv:' . $this->convertSize( $end - $start ) . '<br>';
        //print_r( $tree_array );
        //var_dump( '$final_parent_uid:' . $final_parent_uid );
        //var_dump( $this->parent_uid_array );
        return $this->updateMemberAgentRankUid( $final_parent_uid );
    }

    protected function convertSize( $size )
    {
        $unit = array( 'byte', 'kb', 'mb', 'gb', 'tb', 'pb' );
        return round( $size / pow( 1024, ($i = floor( log( $size, 1024 ) ) ) ), 2 ) . ' ' . $unit[ $i ];
    }

    protected function updateMemberAgentRankUid( $final_parent_uid )
    {
        if ( empty( $final_parent_uid ) ) {
            return true;
        }
        if ( $this->memberInfo->uid == service_Member_base::yph_uid ) {
            return true;
        }
        $this->memberMap[ $this->memberInfo->uid ] = $final_parent_uid;
        $dao = dao_factory_base::getMemberDao();
        $dao->setPk( $this->memberInfo->uid );
        $entity_Member_base = new entity_Member_base();
        $entity_Member_base->agent_rank_uid = $final_parent_uid;
        $entity_Member_base->agent_lock = service_Member_base::agent_lock_yes;
        $entity_Member_base->member_level_time = $this->now;

        return $dao->updateByPk( $entity_Member_base );
    }

    /**
     * 取最终待插的父级uid
     * 暂时不用.因为防止会员下面 有插队排名的情况出来.
     */
    protected function getFinalParentUid()
    {
        //判断$this->last_rank_uid的同级个数有没有3个 last_left_rank_uid:最左边的第一个
        $sibling_array = $this->getSibling( $this->last_rank_uid );
        $parent_uid = $this->memberMap[ $this->last_rank_uid ];
        if ( count( $sibling_array ) < self::tree_node_count ) {
            //同级的还不够3个。就返回父ID    
            return $parent_uid;
        } else {
            //同一层级的全满了,返回上级的下一层
            $parent_uid_key = array_keys( $this->parent_uid_array, $parent_uid );
            if ( empty( $parent_uid_key ) ) {
                //不存在下级.可能是单个会员的下级不是系统下级.这时就找最左边的开始
                return $this->left_rank_uid;
            } else {
                $next_key = $parent_uid_key[ 0 ] + 1;
                $parent_uid_next = array_slice( $this->parent_uid_array, $next_key, 1 );
                return $parent_uid_next[ 0 ];
            }
        }
    }

    /**
     * 处理parent_uid,找last_left_rank_uid的 判断同级有没有. 如果不够3个.直接在后面插入
     * 如果够3个.就看父级的下一个.foreach吧少年
     * @param type $parent_uid
     */
    protected function getSystemFinalParentUid( $tree_array )
    {
//        echo '<pre>';
//        echo 'tree_array:';
//        print_r( $tree_array );
//        die;
        $member_id_array = array();
        foreach ( $tree_array as $value ) {
            foreach ( $value as $uuid ) {
                foreach ( $uuid as $id ) {
                    $member_id_array[] = $id;
                }
            }
        }

        if ( $member_id_array ) {
            $member_id_count = count( $member_id_array );
            foreach ( $member_id_array as $key => $member_id ) {
                if ( $member_id === 0 ) {
                    if ( $key + 1 < $member_id_count ) {
                        prev( $member_id_array );
                        $this->last_left_rank_uid = prev( $member_id_array );
                    } else {
                        $prev_key = $key - 1;
                        $this->last_left_rank_uid = $member_id_array[ $prev_key ];
                    }
                    break;
                }
                $this->last_left_rank_uid = $member_id;
            }
        } else {
            return $this->last_left_rank_uid = $this->root_uid;
        }
        //var_dump( '$this->last_left_rank_uid:' . $this->last_left_rank_uid );
        //判断$this->last_rank_uid的同级个数有没有3个 last_left_rank_uid:最左边的第一个
        $sibling_array = $this->getSibling( $this->last_left_rank_uid );
        $sibling_array = array_filter( $sibling_array ); //过虑空         
        $parent_uid = $this->memberMap[ $this->last_left_rank_uid ];
        if ( count( $sibling_array ) < self::tree_node_count ) {
            //同级的还不够3个。就返回父ID    
            return $parent_uid;
        } else {
            //同一层级的全满了,返回上级的下一层
            //就看父级的下一个.foreach吧少年
            return $this->getNextParent( $parent_uid );
        }
    }

    /**
     * 打印出三叉树结构
     * 找到第一个空位就跳出。0.为空位
     */
    protected function getFirstEmptyTreeArray( $root_uid )
    {
        if ( empty( $this->rank_level ) ) {//如果没有设置指定的层级,用最大的层级
            $this->rank_level = $this->getMemberMaxTreeDepth( $root_uid );
        }
        $level = $this->rank_level;
        $tree_array = array();
        for ( $i = 1; $i <= $level; $i++ ) {
            if ( $i == 1 ) {
                $member_array_count = count( $this->memberArray[ $root_uid ] );
                if ( $member_array_count < self::tree_node_count ) {
                    $member_array_push = self::tree_node_count - $member_array_count;
                    for ( $j = 0; $j < $member_array_push; $j++ ) {
                        array_push( $this->memberArray[ $root_uid ], 0 );
                    }
                    $tree_array[ $i ][] = $this->memberArray[ $root_uid ];
                    //找到第一个0的、跳出
                    break;
                }
                $tree_array[ $i ][] = $this->memberArray[ $root_uid ];
            } else {
                $exp = $i - 1;
                $pre_tree_array = empty( $tree_array[ $exp ] ) ? array() : $tree_array[ $exp ];
                if ( empty( $pre_tree_array ) ) {
                    continue;
                }
                foreach ( $pre_tree_array as $value ) {
                    foreach ( $value as $uid ) {
                        if ( empty( $this->memberArray[ $uid ] ) ) {
                            $tree_array[ $i ][] = array( 0, 0, 0 );
                            //找到第一个0的、跳出
                            break 3;
                        } else {
                            $tree_array[ $i ][] = $this->memberArray[ $uid ];
                        }
                    }
                }
            }
        }
//        echo '<pre>';
//        print_r( $tree_array );

        return $tree_array;
    }

    /**
     * 打印出三叉树结构
     */
    protected function printTreeArray( $root_uid )
    {
        if ( empty( $this->rank_level ) ) {//如果没有设置指定的层级,用最大的层级
            //$this->rank_level = $this->getMemberMaxTreeDepth( $root_uid ); 
            //不能用最大的层级来遍历了。超过15级内存就抗不住了
            $this->rank_level = self::tree_level_count;
        }

        $level = $this->rank_level;
        $tree_array = array();
        for ( $i = 1; $i <= $level; $i++ ) {
            if ( $i == 1 ) {
                if ( empty( $this->memberArray[ $root_uid ] ) ) {
                    break;
                }
                $member_array_count = count( $this->memberArray[ $root_uid ] );
                if ( $member_array_count < self::tree_node_count ) {
                    $member_array_push = self::tree_node_count - $member_array_count;
                    for ( $j = 0; $j < $member_array_push; $j++ ) {
                        array_push( $this->memberArray[ $root_uid ], 0 );
                    }
                }
                $tree_array[ $i ][] = $this->memberArray[ $root_uid ];
            } else {
                $exp = $i - 1;
                $pre_tree_array = empty( $tree_array[ $exp ] ) ? array() : $tree_array[ $exp ];
                if ( empty( $pre_tree_array ) ) {
                    continue;
                }
                foreach ( $pre_tree_array as $value ) {
                    foreach ( $value as $uid ) {
                        $tree_array[ $i ][] = empty( $this->memberArray[ $uid ] ) ? array( 0, 0, 0 ) : $this->memberArray[ $uid ];
                    }
                }
            }
        }
//        echo '<pre>';
//        print_r( $tree_array );

        return $tree_array;
    }

    /**
     * 因为有的子集会独立发展.不能做到系统级的从左到右从上到下. 不用了
     * 系统中只能找到最近的一层.最左边的一个替换0的
     * -------------------------------
     * 处理parent_uid,找last_left_rank_uid的 判断同级有没有. 如果不够3个.直接在后面插入
     * 如果够3个.就看父级的下一个.foreach吧少年
     * @param type $parent_uid
     */
    protected function getSystemFinalParentUidB()
    {
        //判断$this->last_rank_uid的同级个数有没有3个 last_left_rank_uid:最左边的第一个
        $sibling_array = $this->getSibling( $this->last_left_rank_uid );
        $parent_uid = $this->memberMap[ $this->last_left_rank_uid ];
        if ( count( $sibling_array ) < self::tree_node_count ) {
            //同级的还不够3个。就返回父ID    
            return $parent_uid;
        } else {
            //同一层级的全满了,返回上级的下一层
            //就看父级的下一个.foreach吧少年
            return $this->getNextParent( $parent_uid );
        }
    }

    protected function getNextParent( $parent_uid )
    {
        $parent_uid_key = array_keys( $this->parent_uid_array, $parent_uid );
        if ( empty( $parent_uid_key ) ) {
            //不存在下级.可能是单个会员的下级不是系统下级.这时就找最左边的开始            
            Log::getInstance( 'tree_get_next_parent' )->write( var_export( $this->parent_uid_array, true ) . '|$parent_uid:' . var_export( $parent_uid, true ) );
            return 0;
        } else {
            $next_key = $parent_uid_key[ 0 ] + 1;
            $parent_uid_next = array_slice( $this->parent_uid_array, $next_key, 1 );
            $parent_uid_temp = $parent_uid_next[ 0 ];
            $sibling_array = empty( $this->memberArray[ $parent_uid_temp ] ) ? array() : $this->memberArray[ $parent_uid_temp ];
            if ( empty( $sibling_array ) ) {
                return $parent_uid_temp;
            } else if ( count( $sibling_array ) < self::tree_node_count ) {
                return $parent_uid_temp;
            } else {
                return $this->getNextParent( $parent_uid_temp );
            }
        }
    }

    /**
     * 取同级别的数组
     * @param type $uid
     */
    protected function getSibling( $uid )
    {
        if ( $uid == service_Member_base::yph_uid ) {
            return $this->memberArray[ $uid ];
        }
        $parent_uid = $this->memberMap[ $uid ];
        return $this->memberArray[ $parent_uid ];
    }

    /**
     * 遍历所有的排位层级关系  
     * @param type $uid
     * @return boolean
     */
    protected function preMemberTree( $uid )
    {
        //echo $uid . '<br>';
        if ( empty( $this->memberArray[ $uid ] ) ) {
            $this->last_left_rank_uid = $uid;
            return true;
        }
        //左中右
        $member_array = $this->memberArray[ $uid ];
        arsort( $member_array );
        foreach ( $member_array AS $uid ) {
            $this->preMemberTree( $uid );
        }
    }

    /**
     * 层次遍历大法  |广度优先
     * @param type $uid
     * @return boolean
     */
    protected function preMemberTreeTraversal( $uid )
    {
        if ( empty( $this->memberArray[ $uid ] ) ) {
            $this->last_rank_uid = $uid;
            return true;
        }
        $btree = $this->memberArray[ $uid ];
        $queue = array();

        array_unshift( $queue, $btree ); #根节点入队                
        while ( !empty( $queue ) ) { #持续输出节点，直到队列为空
            $member_array = array_pop( $queue ); #队尾元素出队                                    
            $traverse_data[] = $member_array;
            #左节点先入队，然后右节点入队
            foreach ( $member_array AS $uuid ) {
                $this->last_rank_uid = $uuid;
                if ( !empty( $this->memberArray[ $uuid ] ) ) {
                    $member_array_count = count( $this->memberArray[ $uuid ] );
                    if ( $member_array_count < self::tree_node_count ) {
                        $member_array_push = self::tree_node_count - $member_array_count;
                        for ( $i = 0; $i < $member_array_push; $i++ ) {
                            array_push( $this->memberArray[ $uuid ], 0 );
                        }
                    }
                    $btree = $this->memberArray[ $uuid ];
                    array_unshift( $queue, $btree );
                }
            }
        }
        array_push( $this->parent_uid_array, $uid );
        foreach ( $traverse_data as $value ) {
            foreach ( $value as $v ) {
                array_push( $this->parent_uid_array, $v );
            }
        }
        return $traverse_data;
    }

    /**
     * 求最大深度
     * @param type $uid
     * @return boolean
     */
    protected function getMemberMaxTreeDepth( $uid )
    {
//        echo $uid . '<br>';                
        if ( empty( $this->memberArray[ $uid ] ) ) {
            return 0;
        }
        //左中右        
        $left_depth = empty( $this->memberArray[ $uid ][ 0 ] ) ? 0 : $this->getMemberMaxTreeDepth( $this->memberArray[ $uid ][ 0 ] );
        $middle_depth = empty( $this->memberArray[ $uid ][ 1 ] ) ? 0 : $this->getMemberMaxTreeDepth( $this->memberArray[ $uid ][ 1 ] );
        $right_depth = empty( $this->memberArray[ $uid ][ 2 ] ) ? 0 : $this->getMemberMaxTreeDepth( $this->memberArray[ $uid ][ 2 ] );

        $array = array( $left_depth, $middle_depth, $right_depth );
        return max( $array ) + 1;
    }

    /**
     * 求最大深度
     * 如果错误日志中 tree_get_next_parent 没有。就可以删除
     * toto delete
     * @param type $uid
     * @return boolean
     */
    protected function getLeftRankUId( $uid )
    {
        //echo $uid . '<br>';
        $this->left_rank_uid = $uid;
        if ( empty( $this->memberArray[ $uid ] ) ) {
            return true;
        }
        //左中右
        foreach ( $this->memberArray[ $uid ] as $key => $uid ) {
            if ( $key == 0 ) {
                //var_dump( $uid . $this->rank_level );
                $this->rank_level++;
                $this->getMemberMaxTreeDepth( $uid );
            }
        }
        return true;
    }

    /**
     * 求某个值为x的节点的深度
     * @param type $uid
     * @return boolean
     */
    protected function getMemberLastRankUidTreeDepth( $root_uid, $uid )
    {
        //echo $uid . '<br>';        
        if ( $uid == $root_uid ) {
            return true;
        }
        if ( empty( $this->memberMap[ $uid ] ) ) {
            return true;
        }
        $this->last_rank_level++;
        $parent_uid = $this->memberMap[ $uid ];
        return $this->getMemberLastRankUidTreeDepth( $root_uid, $parent_uid );
    }

}
