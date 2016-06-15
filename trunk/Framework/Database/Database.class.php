<?php

/** Power By Tmac PHP MVC framework
 *  $Author: zhangwentao $
 *  $Id: Database.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */
abstract class Database
{

    /**
     * 数据库连接标识
     * 
     * @var resource
     */
    protected $link;

    /**
     * 执行SQL语句的次数
     *
     * @var integer
     * @access protected
     */
    protected $queryNum = 0;

    /**
     * 返回或者影响记录数
     * @var type 
     */
    protected $numRows = 0;

    /**
     * 缓存实例
     *
     * @var objeact
     * @access protected
     */
    protected $cache;
    protected $is_cache = false;

    /**
     * 上一次事物是否执行成功
     *
     * @var bool
     * @access protected
     */
    protected $success = true;

    /**
     * 事务初始状态 以确定是否应该发生回滚
     * @var type 
     */
    protected $trans_status = false;

    /**
     * 数据库左标识符
     * @var type 
     */
    protected $identifier_left = '`';

    /**
     * 数据库右标识符
     * @var type 
     */
    protected $identifier_right = '`';

    /**
     * 连接数据库
     */
    public abstract function connect ();

    /**
     * 选择数据库
     *
     * @param string $database
     */
    public abstract function selectDatabase ( $database );

    /**
     * 执行一条SQL查询语句 返回资源标识符
     * 
     * @param string $sql
     */
    public abstract function query ( $sql );

    /**
     * 执行一条SQL语句 返回似乎执行成功
     *
     * @param string $sql
     */
    public abstract function execute ( $sql );

    /**
     * 从结果集中取出数据
     * 
     * @param resource $rs
     */
    protected abstract function fetch ( $rs );

    /**
     * 从结果集中取出对象
     * 
     * @param resource $rs
     */
    protected abstract function fetch_object ( $rs );

    /**
     * 执行INSERT命令.返回AUTO_INCREMENT
     * 返回0为没有插入成功
     *
     * @param string $sql  SQL语句
     * @access public
     * @return integer
     */
    public abstract function insert ( $sql );

    /**
     * 释放结果集
     *
     * @param resource $rs 结果集
     * @access protected
     * @return boolean
     */
    protected abstract function free ( $rs );

    /**
     * 关闭数据库
     *
     * @access public
     * @return boolean
     */
    public abstract function close ();

    /**
     * 获取错误信息
     *
     * @return void
     * @access public
     */
    public abstract function getError ();

    /**
     * 设置debug打开关闭
     * @param type $open 
     */
    public function setDebug ( $open = false )
    {
        $GLOBALS[ 'TmacConfig' ][ 'Common' ][ 'debug' ] = $open;
    }

    /**
     * 获取执行SQL语句的个数
     *
     * @access public
     * @return integer
     */
    public function getQueryNum ()
    {
        return $this->queryNum;
    }

    /**
     * 取得前一次 MySQL 操作所影响的记录行数 Its an DELETE, INSERT, REPLACE, or UPDATE query
     * @return type 
     */
    public function getNumRows ()
    {
        return $this->numRows;
    }

    /**
     * 获取缓存实例
     *
     * @access protected
     * @final
     */
    protected final function getCache ()
    {
        $this->cache = CacheDriver::getInstance ();
        $this->is_cache = true;
    }

    /**
     * 得到结果集的第一个数据
     *
     * @param string $sql   SQL语句
     * @access public
     * @return mixed
     */
    public function getOne ( $sql )
    {
        if ( !$rs = $this->query ( $sql ) ) {
            return false;
        }
        $row = $this->fetch ( $rs );
        $this->free ( $rs );
        return is_array ( $row ) ? array_shift ( $row ) : $row;
    }

    /**
     * 以缓存的方式获取结果集的第一个数据
     *
     * @param string $sql   SQL语句
     * @param int $expire   缓存时间
     * @return mixed
     * @access public
     */
    public function cacheGetOne ( $sql, $expire = 60 )
    {
        $this->is_cache OR $this->getCache ();
        $value = $this->cache->get ( $sql );
        if ( $value === false ) {
            $value = $this->getOne ( $sql );
            $this->cache->set ( $sql, $value, $expire );
        }
        return $value;
    }

    /**
     * 返回结果集的一行
     *
     * @param string $sql  SQL语句
     * @access public
     * @return array
     */
    public function getRow ( $sql )
    {
        if ( !$rs = $this->query ( $sql ) ) {
            return false;
        }
        $row = $this->fetch ( $rs );
        $this->free ( $rs );
        return $row;
    }

    /**
     * 以缓存的方式取得结果集的第一行
     *
     * @param string $sql   SQL语句
     * @param int $expire   缓存时间
     * @return array
     * @access public
     */
    public function cacheGetRow ( $sql, $expire = 60 )
    {
        $this->is_cache OR $this->getCache ();
        $value = $this->cache->get ( $sql );
        if ( $value === false ) {
            $value = $this->getRow ( $sql );
            $this->cache->set ( $sql, $value, $expire );
        }
        return $value;
    }

    /**
     * 返回结果集的一条对象
     *
     * @param string $sql  SQL语句
     * @access public
     * @return array
     */
    public function getRowObject ( $sql )
    {
        if ( !$rs = $this->query ( $sql ) ) {
            return false;
        }
        $row = $this->fetch_object ( $rs );
        $this->free ( $rs );
        return $row;
    }

    /**
     * 返回所有结果集
     *
     * @param string $sql   SQL语句
     * @access public
     * @return mixed
     */
    public function getAll ( $sql )
    {
        if ( !$rs = $this->query ( $sql ) ) {
            return false;
        }
        $all_rows = array();
        while ( $rows = $this->fetch ( $rs ) ) {
            $all_rows[] = $rows;
        }
        $this->free ( $rs );
        return $all_rows;
    }

    /**
     * 以缓存的方式取得所有结果集
     *
     * @param string $sql   SQL语句
     * @param int $expire   缓存时间
     * @return array
     * @access public
     */
    public function cacheGetAll ( $sql, $expire = 60 )
    {
        $this->is_cache OR $this->getCache ();
        $value = $this->cache->get ( $sql );
        if ( $value === false ) {
            $value = $this->getAll ( $sql );
            $this->cache->set ( $sql, $value, $expire );
        }
        return $value;
    }

    /**
     * 返回所有结果集对象模式
     *
     * @param string $sql   SQL语句
     * @access public
     * @return mixed
     */
    public function getAllObject ( $sql )
    {
        if ( !$rs = $this->query ( $sql ) ) {
            return false;
        }
        $all_rows = array();
        while ( $rows = $this->fetch_object ( $rs ) ) {
            $all_rows[] = $rows;
        }
        $this->free ( $rs );
        return $all_rows;
    }

    /**
     * 取所有行的第一个字段信息
     *
     * @param string $sql   SQL语句
     * @return array
     * @access public
     */
    protected function getCol ( $sql )
    {
        $res = $this->query ( $sql );
        if ( $res !== false ) {
            $arr = array();
            while ( $row = $this->fetch_row ( $res ) ) {
                $arr[] = $row[ 0 ];
            }

            return $arr;
        } else {
            return false;
        }
    }

    /**
     * 以缓存方式取所有行的第一个字段信息
     *
     * @param string $sql   SQL语句
     * @param int $expire   缓存时间
     * @return array
     * @access public
     */
    protected function cachegetCol ( $sql, $expire = 60 )
    {
        $this->is_cache OR $this->getCache ();
        $value = $this->cache->get ( $sql );
        if ( $value === false ) {
            $value = $this->getCol ( $sql );
            $this->cache->set ( $sql, $value, $expire );
        }
        return $value;
    }

    /**
     * insert update For Mysql
     * @param <type> $table
     * @param <type> $field_values
     * @param <type> $mode
     * @param <type> $where
     * @return <type>
     */
    public function autoExecute ( $table, $field_values, $mode = 'INSERT', $where = '' )
    {
        $field_names = $this->getCol ( 'DESC ' . $table );

        $sql = '';
        if ( $mode == 'INSERT' ) {
            $fields = $values = array();
            foreach ( $field_names AS $value ) {
                if ( array_key_exists ( $value, $field_values ) == false ) {
                    continue;
                }
                $fields[] = '`' . $value . '`';
                if ( $field_values[ $value ] instanceof TmacDbExpr ) {
                    $values[] = $field_values[ $value ];
                } else {
                    $values[] = $this->escape ( $field_values[ $value ] );
                }
            }
            $sql = $this->getInsertSql ( $table, $fields, $values );
        } else {
            $sets = array();
            foreach ( $field_names AS $value ) {
                if ( array_key_exists ( $value, $field_values ) == false ) {
                    continue;
                }
                if ( $field_values[ $value ] instanceof TmacDbExpr ) {
                    $sets[] = '`' . $value . '` = ' . $field_values[ $value ];
                } else {
                    $sets[] = '`' . $value . '` = ' . $this->escape ( $field_values[ $value ] );
                }
            }

            $sql = $this->getUpdateSql ( $table, $sets, $where );
        }

        if ( $sql ) {
            return $this->query ( $sql );
        } else {
            return false;
        }
    }

    /**
     * insert For Mysql return 有返回值 返回mysql_insert_id
     * @param <type> $table
     * @param <type> $field_values
     * @param <type> $mode
     * @param <type> $where
     * @return <type>
     */
    public function autoInsertReturn ( $table, $field_values )
    {
        $field_names = $this->getCol ( 'DESC ' . $table );

        $sql = '';
        $fields = $values = array();
        foreach ( $field_names AS $value ) {
            if ( array_key_exists ( $value, $field_values ) == true ) {
                $fields[] = '`' . $value . '`';
                if ( $field_values[ $value ] instanceof TmacDbExpr ) {
                    $values[] = $field_values[ $value ];
                } else {
                    $values[] = $this->escape ( $field_values[ $value ] );
                }
            }
        }

        $sql = $this->getInsertSql ( $table, $fields, $values );

        if ( $sql ) {
            return $this->insert ( $sql );
        } else {
            return false;
        }
    }

    public function insertObject ( $table, $object )
    {
        $entity_table_name = get_class ( $object );
        $entity_table_class = new $entity_table_name();

        $fields = $values = array();
        foreach ( $object as $key => $value ) {
            if ( isset ( $value ) === false ) {//排除掉对象值为空的
                continue;
            }
            if ( property_exists ( $entity_table_class, $key ) === false ) {//排除掉数据库实体中不存在的
                continue;
            }
            $fields[] = $this->identifier_left . $key . $this->identifier_right;
            if ( $value instanceof TmacDbExpr ) {
                $values[] = $value;
            } else {
                $values[] = $this->escape ( $value );
            }
        }
        $sql = $this->getInsertSql ( $table, $fields, $values );
        if ( $sql ) {
            return $this->insert ( $sql );
        } else {
            return false;
        }
    }

    /**
     * 更新数据实体
     * @param type $table 表名
     * @param type $object 表实据实体
     * @param type $where 更新条件
     * @param type $primaryKeyField 如果不为空，则是主键更新时的主键名称
     * @return boolean
     */
    public function updateObject ( $table, $object, $where, $primaryKeyField = '' )
    {
        $entity_table_name = get_class ( $object );
        $entity_table_class = new $entity_table_name();

        $sets = array();
        foreach ( $object as $key => $value ) {
            if ( isset ( $value ) === false ) {//排除掉对象值为空的
                continue;
            }
            if ( property_exists ( $entity_table_class, $key ) === false ) {//排除掉数据库实体中不存在的
                continue;
            }
            if ( !empty ( $primaryKeyField ) && $key === $primaryKeyField ) {//排除掉主键更新时的主键字段的误更新
                continue;
            }
            if ( $value instanceof TmacDbExpr ) {
                $sets[] = $this->identifier_left . $key . $this->identifier_right . ' = ' . $value;
            } else {
                $sets[] = $this->identifier_left . $key . $this->identifier_right . ' = ' . $this->escape ( $value );
            }
        }
        $sql = $this->getUpdateSql ( $table, $sets, $where );
        if ( $sql ) {
            return $this->execute ( $sql );
        } else {
            return false;
        }
    }

    /**
     * 获取上次事物是否执行成功
     * 
     * @return bool
     */
    public function isSuccess ()
    {
        return $this->success;
    }

    /**
     * 通过 表名，字段数组，值数组 返回组合的sql
     * @param type $table
     * @param type $fields
     * @param type $values
     * @return string
     */
    protected function getInsertSql ( $table, $fields, $values )
    {
        $sql = false;
        if ( !empty ( $fields ) ) {
            $sql = 'INSERT INTO ' . $table . ' (' . implode ( ', ', $fields ) . ') VALUES (' . implode ( ', ', $values ) . ')';
        }
        return $sql;
    }

    /**
     * 通过 表名，filed=$value, $where条件
     * @param type $table
     * @param type $sets
     * @param type $where
     * @return string
     */
    protected function getUpdateSql ( $table, $sets, $where )
    {
        $sql = false;
        if ( !empty ( $sets ) ) {
            $sql = 'UPDATE ' . $table . ' SET ' . implode ( ', ', $sets ) . ' WHERE ' . $where;
        }
        return $sql;
    }

    /**
     * "Smart" Escape String
     *
     * Escapes data based on type
     * Sets boolean and null types
     *
     * @access	public
     * @param	string
     * @return	mixed
     */
    protected function escape ( $str )
    {
        if ( is_string ( $str ) ) {
            $str = "'" . $str . "'";
        } elseif ( is_bool ( $str ) ) {
            $str = ($str === FALSE) ? 0 : 1;
        } elseif ( is_null ( $str ) ) {
            $str = 'NULL';
        }

        return $str;
    }

    /**
     * DEBUG信息
     * 
     * @param string $sql
     * @param bool $success
     * @param error string
     */
    public function debug ( $sql, $success = true, $error = null )
    {
        global $TmacConfig;
        if ( $TmacConfig[ 'Common' ][ 'debug' ] ) {
            $debug = Debug::getInstance ();
            $debug->setSQL ( $sql, $success, $error );
        }
    }

}
