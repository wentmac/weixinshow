<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: EntityCreate.class.php 330 2016-06-01 14:04:25Z zhangwentao $
 */

/**
 * Description of EntityCreate
 *
 * @author Tracy McGrady
 */
class service_EntityCreate_base extends Model
{

    protected $db;
    private $table_name;
    private $entity_class_dir;
    private $db_prefix = 'yph';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connect();
        $this->entity_class_dir = TMAC_BASE_PATH . APP_BASE_NAME . DIRECTORY_SEPARATOR . APPLICATION . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'entity' . DIRECTORY_SEPARATOR;
    }

    public function setDb_prefix( $db_prefix )
    {
        $this->db_prefix = $db_prefix;
    }

    public function getDb_prefix()
    {
        return $this->db_prefix;
    }

    public function getTable_name()
    {
        return $this->table_name;
    }

    public function setTable_name( $table_name )
    {
        $this->table_name = $table_name;
    }

    public function getTableArray()
    {
        $result = $this->db->query( "SHOW TABLE STATUS" );
        $tableArray = array();
        while ( $row = $this->db->fetch( $result ) ) {
            $tableArray[] = $row;
        }
        return $tableArray;
    }

    public function entityCreate()
    {
        $fieldArray = $this->getTableField();
        $entityFileName = $this->getEntityFileName();
        $date = date( "Y-m-d H:i:s" );
        if ( $fieldArray ) {
            $fileContent = '<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $ 
 */

/**
 * Description of ' . $entityFileName[ 'name' ] . '
 *
 * @author Tracy McGrady
 */
class entity_' . $entityFileName[ 'classname' ] . '_base
{
';
            foreach ( $fieldArray AS $field ) {
                $fileContent.='    public $' . $field . ';';
                $fileContent .= "\r\n";
            }
            $fileContent .= '}';
            file_put_contents( $entityFileName[ 'file' ], $fileContent, LOCK_EX );
            echo $entityFileName[ 'file' ] . '生成成功<br>';
        }
    }

    /**
     * 生成实体时用来取表里的字段名（仅能取到字段名）
     * @return type
     */
    private function getTableField()
    {
        $result = $this->db->query( "SELECT * FROM {$this->getTable_name()} LIMIT 0,1" );
        $fieldArray = array();
        if ( $result ) {                        
            if ( $this->db->config['dbdriver'] === 'MySQLi' ) {
                while ( $fields = mysqli_fetch_field( $result ) ) {
                    $fieldArray[] = $fields->name;
                }
            } else {
                while ( $fields = mysql_fetch_field( $result ) ) {
                    $fieldArray[] = $fields->name;
                }
            }
        }
        return $fieldArray;
    }

    private function getEntityFileName()
    {
        $file_name_array = explode( '_', $this->getTable_name() );
        $file_name = '';
        foreach ( $file_name_array AS $k => $v ) {
            if ( $k == 0 && $v == $this->getDb_prefix() ) {
                continue;
            }
            $file_name.=ucwords( $v );
        }
        $file = $this->entity_class_dir . $file_name . '.class.php';
        return array( 'file' => $file, 'name' => $file_name . '.class.php', 'classname' => $file_name );
    }

    public function getTableInfo()
    {
        $sql = "SHOW FULL FIELDS FROM `" . $this->getTable_name() . "`";
        $result = $this->db->query( $sql );
        $fieldArray = array();
        if ( $result ) {
            while ( $rs = $this->db->fetch( $result ) ) {
                $res = array();
                preg_match( "/([a-z]+)\(([0-9]+)\)(\sa-z)*/i", $rs[ 'Type' ], $matches );
                preg_match( "/([a-z]+)*/i", $rs[ 'Type' ], $match );
                $res[ 'field' ] = $rs[ 'Field' ];
                $res[ 'name' ] = empty( $matches[ 1 ] ) ? $match[ 0 ] : $matches[ 1 ];
                $res[ 'max_length' ] = empty( $matches[ 2 ] ) ? '' : $matches[ 2 ];
                $res[ 'description' ] = $rs[ 'Comment' ];
                $res[ 'key' ] = $rs[ 'Key' ];
                $fieldArray[] = $res;
            }
        }
        return $fieldArray;
    }

}
