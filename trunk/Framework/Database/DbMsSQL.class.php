<?php

/**
 *  Power By Tmac PHP MVC framework
 *  $Author: zhangwentao $
 *  $Id: DbMsSQL.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 *  +-------------------------------------------------------------
 *  MSsql数据库驱动类
 *  +-------------------------------------------------------------
 */
class DbMsSQL extends Database
{

    private $errsql;

    /**
     * 初始化
     *
     * @global array $TmacConfig
     */
    public function __construct( $config )
    {
        $this->config = $config;
        //连接数据库
        $this->link = $this->connect();
        if ( $this->link ) {
            //选择数据库
            $this->selectDatabase( $this->config[ 'database' ] );
            //设置编码与sql_mode
//            mssql_query("SET NAMES '{$this->config['char_set']}', sql_mode=''", $this->link);            
        } else {
            throw new TmacException( '无法连接到数据库:' . $this->getError() );
        }
        $this->identifier_left = '[';
        $this->identifier_right = ']';
    }

    /**
     * 连接数据库
     *
     * @global array $TmacConfig
     * @return bool
     */
    public function connect()
    {
        $fun = $this->config[ 'pconnect' ] ? 'mssql_pconnect' : 'mssql_connect';
        return $fun( $this->config[ 'hostname' ] . ':' . $this->config[ 'port' ], $this->config[ 'username' ], $this->config[ 'password' ], true );
    }

    /**
     * 选择数据库
     *
     * @param string $database
     * @return bool
     */
    public function selectDatabase( $database )
    {
        return mssql_select_db( $database, $this->link );
    }

    /**
     * 执行一条SQL查询语句 返回资源标识符
     *
     * @param string $sql
     */
    public function query( $sql )
    {
        $this->errsql = $sql;
        $rs = mssql_query( $sql, $this->link );
        if ( $rs ) {
            $this->queryNum++;
            $this->numRows = mssql_rows_affected( $this->link );
            $this->debug( $sql );
            return $rs;
        } else {
            $this->debug( $sql, false, $this->getError() );
            $this->success = false;
            return false;
        }
    }

    /**
     * 执行一条SQL语句 返回似乎执行成功
     *
     * @param string $sql
     */
    public function execute( $sql )
    {
        $this->errsql = $sql;
        if ( mssql_query( $sql, $this->link ) ) {
            $this->queryNum++;
            $this->numRows = mssql_rows_affected( $this->link );
            $this->debug( $sql );
            return true;
        } else {
            $this->debug( $sql, false, $this->getError() );
            $this->success = false;
            return false;
        }
    }

    /**
     * 从结果集中取出数据
     *
     * @param resource $rs
     */
    public function fetch( $rs )
    {
        return mssql_fetch_array( $rs, MSSQL_ASSOC );
    }

    /**
     * 从结果集中取出对象
     *
     * @param resource $rs
     */
    public function fetch_object( $rs )
    {
        return mssql_fetch_object( $rs );
    }

    /**
     * 返回结果集的数组形式row
     * @param <type> $result
     * @return <type>
     */
    public function fetch_row( $result )
    {
        return mssql_fetch_row( $result );
    }

    /**
     * 开始事务
     *
     * @return bool
     */
    public function startTrans()
    {
        if ( !$this->link )
            return false;
        if ( !$this->trans_status ) { //如果当前事务状态是关闭 启动事务
            $this->trans_status = $this->execute( 'BEGIN TRANSACTION' );
        }
        return $this->trans_status;
    }

    /**
     * 提交事务
     *
     * @return bool
     */
    public function commit()
    {
        if ( $this->trans_status ) { //如果当前事务状态开启 才能提交
            $this->execute( 'COMMIT TRANSACTION' );
            $this->trans_status = false;    //事务回滚完毕后 关闭事务的开始状态
        }
        return true;
    }

    /**
     * 回滚事务
     *
     * @return bool
     */
    public function rollback()
    {
        if ( $this->trans_status ) { //如果当前事务状态开启 才能回滚
            $this->execute( 'ROLLBACK TRANSACTION' );
            $this->trans_status = false; //事务回滚完毕后 关闭事务的开始状态
            if ( $this->success === FALSE ) {
                $this->success = TRUE;
            }
        }
        return true;
    }

    /**
     * 执行INSERT命令.返回AUTO_INCREMENT
     * 返回0为没有插入成功
     *
     * @param string $sql  SQL语句
     * @access public
     * @return integer
     */
    public function insert( $sql )
    {
        $this->execute( $sql );
        return $this->mssql_insert_id();
    }

    /**
      +----------------------------------------------------------
     * 用于获取最后插入的ID
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return integer
      +----------------------------------------------------------
     */
    public function mssql_insert_id()
    {
        $query = "SELECT @@IDENTITY as last_insert_id";
        $result = mssql_query( $query, $this->link );
        list($last_insert_id) = mssql_fetch_row( $result );
        mssql_free_result( $result );
        return $last_insert_id;
    }

    /**
     * 释放结果集
     *
     * @param resource $rs 结果集
     * @access protected
     * @return boolean
     */
    protected function free( $rs )
    {
        return mssql_free_result( $rs );
    }

    /**
     * 关闭数据库
     *
     * @access public
     * @return boolean
     */
    public function close()
    {
        return mssql_close( $this->link );
    }

    /**
     * 获取错误信息
     *
     * @return void
     * @access public
     */
    public function getError()
    {
        $error = mssql_get_last_message();
        return 'MsSQL Error: ' . $error . "<br>" . $this->errsql;
    }

    /**
     * insert update For Mssql
     * @param <type> $table
     * @param <type> $field_values
     * @param <type> $mode
     * @param <type> $where
     * @return <type>
     */
    function autoExecuteMssql( $table, $field_values, $mode = 'INSERT', $where = '' )
    {
        $sql = '';
        if ( $mode == 'INSERT' ) {
            $fields = $values = array();
            foreach ( $field_values AS $key => $value ) {
                $fields[] = '' . $key . '';
                if ( $value instanceof TmacDbExpr ) {
                    $values[] = $value;
                } else {
                    $values[] = "'" . $value . "'";
                }
            }
            $sql = $this->getInsertSql( $table, $fields, $values );
        } else {
            $sets = array();
            foreach ( $field_values AS $key => $value ) {
                if ( $value instanceof TmacDbExpr ) {
                    $sets[] = '[' . $key . '] = ' . $field_values[ $value ];
                } else {
                    $sets[] = "[{$key}] = '{$value}'";
                }
            }
            $sql = $this->getUpdateSql( $table, $sets, $where );
        }
        if ( $sql ) {
            return $this->query( $sql );
        } else {
            return false;
        }
    }

    /**
     * Closes the database connection.
     */
    public function __destruct()
    {
        is_resource( $this->link ) && self::close();
    }

}
