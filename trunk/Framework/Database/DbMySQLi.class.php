<?php

/**
 *  Power By Tmac PHP MVC framework
 *  $Author: zhangwentao $
 *  $Id: DbMySQLi.class.php 325 2016-05-31 10:07:35Z zhangwentao $
 */
class DbMySQLi extends Database
{

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
            //设置编码与sql_mode
            mysqli_query( $this->link, "SET NAMES '{$this->config[ 'char_set' ]}', sql_mode=''" );
        } else {
            throw new TmacException( '无法连接到数据库:' . $this->getError() );
        }
    }

    /**
     * 连接数据库
     *
     * @global array $TmacConfig
     * @return bool
     */
    public function connect()
    {
        $fun = $this->config[ 'pconnect' ] ? 'mysqli_pconnect' : 'mysqli_connect';
        return $fun( $this->config[ 'hostname' ], $this->config[ 'username' ], $this->config[ 'password' ], $this->config[ 'database' ], $this->config[ 'port' ] );
    }

    /**
     * 选择数据库
     *
     * @param string $database
     * @return bool
     */
    public function selectDatabase( $database )
    {
        return mysqli_select_db( $database, $this->link );
    }

    /**
     * 执行一条SQL查询语句 返回资源标识符
     *
     * @param string $sql
     */
    public function query( $sql )
    {
        $rs = mysqli_query( $this->link, $sql );
        if ( $rs ) {
            $this->queryNum++;
            $this->numRows = mysqli_affected_rows( $this->link );
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
        if ( mysqli_query( $this->link, $sql ) ) {
            $this->queryNum++;
            $this->numRows = mysqli_affected_rows( $this->link );
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
        return mysqli_fetch_array( $rs, MYSQL_ASSOC );
    }

    /**
     * 从结果集中取出对象
     *
     * @param resource $rs
     */
    public function fetch_object( $rs )
    {
        return mysqli_fetch_object( $rs );
    }

    /**
     * 返回结果集的数组形式row
     * @param <type> $result
     * @return <type>
     */
    public function fetch_row( $result )
    {
        return mysqli_fetch_row( $result );
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
            $this->trans_status = $this->execute( 'START TRANSACTION' );
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
            $this->execute( 'COMMIT' );
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
            $this->execute( 'ROLLBACK' );
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
        return mysqli_insert_id( $this->link );
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
        return mysqli_free_result( $rs );
    }

    /**
     * 关闭数据库
     *
     * @access public
     * @return boolean
     */
    public function close()
    {
        return mysqli_close( $this->link );
    }

    /**
     * 获取错误信息
     *
     * @return void
     * @access public
     */
    public function getError()
    {
        return mysqli_errno( $this->link ) . " : " . mysqli_error( $this->link );
    }

    /**
     * Closes the database connection.
     */
    public function __destruct()
    {
        is_resource( $this->link ) && self::close();
    }

}
