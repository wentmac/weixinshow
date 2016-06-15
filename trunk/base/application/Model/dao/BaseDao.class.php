<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: BaseDao.class.php 366 2016-06-13 11:01:36Z zhangwentao $
 */

/**
 * Description of BaseDao
 *
 * @author Tracy McGrady
 */
abstract class dao_BaseDao_base
{

    protected $db;
    private $PrimaryKeyField; //主键字段名
    protected $pk; //主键
    protected $table;
    protected $field = '*';
    protected $count_field = '*';
    protected $where;
    protected $orderby;
    protected $groupby;
    protected $limit;
    protected $top;
    protected $joinString;

    /**
     * 初始化
     *
     * @global array $TmacConfig
     */
    public function __construct( $link_identifier )
    {
        $this->db = $link_identifier;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getOrderby()
    {
        return $this->orderby;
    }

    function getGroupby()
    {
        return $this->groupby;
    }

    public function setField( $field )
    {
        $this->field = $field;
    }

    function setCount_field( $count_field )
    {
        $this->count_field = $count_field;
    }

    function setGroupby( $groupby )
    {
        $this->groupby = $groupby;
    }

    public function setOrderby( $orderby )
    {
        $this->orderby = $orderby;
    }

    public function getWhere()
    {
        return $this->where;
    }

    public function setWhere( $where )
    {
        $this->where = $where;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit( $limit )
    {
        $this->limit = $limit;
    }

    public function getTop()
    {
        return $this->top;
    }

    public function setTop( $top )
    {
        $this->top = $top;
    }

    public function getPk()
    {
        return $this->pk;
    }

    public function setPk( $pk )
    {
        $this->pk = $pk;
    }

    protected function getPrimaryKeyField()
    {
        return $this->PrimaryKeyField;
    }

    protected function setPrimaryKeyField( $PrimaryKeyField )
    {
        $this->PrimaryKeyField = $PrimaryKeyField;
    }

    /**
     * 通过主键取数据库信息
     * @return type
     */
    public function getInfoByPk()
    {
        $sql = "SELECT {$this->getField()} "
                . "FROM {$this->getTable()} "
                . "WHERE {$this->getPrimaryKeyField()}={$this->getPk()}";
        $res = $this->getDb()->getRowObject( $sql );
        return $res;
    }

    /**
     * 通过$where条件取数据库信息
     * @return type
     */
    public function getInfoByWhere()
    {
        $sql = "SELECT {$this->getField()} "
                . "FROM {$this->getTable()} ";
        if ( $this->joinString != null ) {
            $sql .= "{$this->joinString} ";
        }
        $sql.= "WHERE {$this->getWhere()}";
        if ( $this->getOrderby() != null ) {
            $sql .= " ORDER BY {$this->getOrderby()} ";
        }
        $res = $this->getDb()->getRowObject( $sql );
        return $res;
    }

    /**
     * 通过$where条件取多条数据库信息
     * @return type
     */
    public function getListByWhere()
    {
        $sql = $this->getSqlByWhere();
        $res = $this->getDb()->getAllObject( $sql );
        return $res;
    }

    /**
     * 通过setWhere等方法来取查询的最终sql;
     * 主要是给UNION 或 UNION ALL用的  where IN($sql)
     * $query1= $dao->getSqlByWhere();
     * $query2= $dao->getSqlByWhere();
     * $res = $dao->getDb()->getAllObject($query1." UNION ".$query2);
     * 
     * @return type
     */
    public function getSqlByWhere()
    {
        $sql = "SELECT ";
        if ( $this->getTop() != null ) {
            $sql .= "TOP {$this->getTop()} ";
        }
        $sql .= "{$this->getField()} "
                . "FROM {$this->getTable()} ";

        if ( $this->joinString != null ) {
            $sql .= "{$this->joinString} ";
        }
        if ( $this->getWhere() != null ) {
            $sql .= "WHERE {$this->getWhere()} ";
        }
        if ( $this->getGroupby() != null ) {
            $sql .= "GROUP BY {$this->getGroupby()} ";
        }
        if ( $this->getOrderby() != null ) {
            $sql .= "ORDER BY {$this->getOrderby()} ";
        }
        if ( $this->getLimit() != null ) {
            $sql .= "LIMIT {$this->getLimit()}";
        }
        return $sql;
    }

    /**
     * 通过$where条件取总数     
     * @return type 
     */
    public function getCountByWhere()
    {
        $sql_count = "SELECT COUNT({$this->count_field}) FROM {$this->getTable()} ";
        if ( $this->getWhere() != null ) {
            $sql_count .= "WHERE " . $this->getWhere();
        }
        $count = $this->getDb()->getOne( $sql_count );
        return $count;
    }

    /**
     * 通过主键更新数据
     * @param type $entity
     * @return boolean
     */
    public function updateByPk( $entity )
    {
        if ( empty( $this->pk ) ) {
            return false;
        }
        $where = $this->getPrimaryKeyField() . '=' . $this->pk;
        $rs = $this->getDb()->updateObject( $this->getTable(), $entity, $where, $this->getPrimaryKeyField() );
        return $rs;
    }

    /**
     * 通过$where条件更新数据
     * @param type $entity
     * @return type
     */
    public function updateByWhere( $entity )
    {
        $rs = $this->getDb()->updateObject( $this->getTable(), $entity, $this->getWhere() );
        return $rs;
    }

    /**
     * 插入数据
     * @param type $entity
     * @return type
     */
    public function insert( $entity )
    {
        return $this->getDb()->insertObject( $this->getTable(), $entity );
    }

    /**
     * 通过主键删除一条记录{删除数据的操作请慎用}
     * @return type
     */
    public function deleteByPk()
    {
        $sql = "DELETE FROM {$this->getTable()} "
                . "WHERE {$this->getPrimaryKeyField()}={$this->getPk()}";
        $res = $this->getDb()->execute( $sql );
        return $res;
    }

    /**
     * 通过$where条件删除N条记录{删除数据的操作请慎用}
     * @return type
     */
    public function deleteByWhere()
    {
        $sql = "DELETE FROM {$this->getTable()} "
                . "WHERE {$this->getWhere()}";
        $res = $this->getDb()->execute( $sql );
        return $res;
    }

    /**
     * 取有可能有where in的语句
     * @param type $field
     * @param type $value
     * @return type
     */
    public function getWhereInStatement( $field, $value )
    {
        if ( strpos( $value, ',' ) !== false ) {
            return "{$field} IN({$value}) ";
        } else {
            return "{$field} ={$value} ";
        }
    }

    /**
     * join子句查询
     * 支持  left|right|outer|inner|left outer|right outer
     * 
     * $dao = dao_factory_base::getGoodsDao();
     * $goods_image_dao = dao_factory_base::getGoodsImageDao();
     * $dao->join($goods_image_dao->getTable(),$goods_image_dao->getTable().'goods_id='.$dao->getTable().'.goods_id','left');
     * $dao->setWhere($goods_image_dao->getTable().'.uid='.$uid);
     * $res = $dao->getListByWhere();
     * 
     * @param type $joinTable 表名
     * @param type $on join时候的on语句
     * @param type $joinType 联表的类型
     * @return type
     */
    public function join( $joinTable, $on, $joinType = '' )
    {
        $joinTypeArray = array( 'LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER' );
        $joinTypeString = 'JOIN'; //默认joinType为空时 是JOIN
        if ( !empty( $joinType ) ) {
            $joinType = strtoupper( $joinType );
            if ( in_array( $joinType, $joinTypeArray ) ) {
                $joinTypeString = $joinType . ' JOIN';
            }
        }
        $this->joinString = $joinTypeString . ' ' . $joinTable . ' ON ' . $on;
        return $this->joinString;
    }

}
