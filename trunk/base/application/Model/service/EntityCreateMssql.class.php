<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $
 * $Id: EntityCreateMssql.class.php 330 2016-06-01 14:04:25Z zhangwentao $
 */

/**
 * Description of EntityCreate
 *
 * @author Tracy McGrady
 */
class service_EntityCreateMssql_base extends Model
{

    protected $db;
    private $table_name;
    private $entity_class_dir;
    private $db_prefix = 'yph';

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connect('booking_zhuna_cn');
        $this->entity_class_dir = TMAC_BASE_PATH . APP_BASE_NAME . DIRECTORY_SEPARATOR . APPLICATION . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'entity' . DIRECTORY_SEPARATOR;
    }

    public function setDb_prefix($db_prefix)
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

    public function setTable_name($table_name)
    {
        $this->table_name = $table_name;
    }

    public function getTableArray()
    {
//        $sql = 'select * from sys.tables';
        //$sql = 'SELECT b.name AS Name,value as Comment FROM sys.extended_properties a left JOIN  sysobjects b ON a.major_id=b.id where a.minor_id=0 and a.name=\'MS_Description\'';
        $sql = 'SELECT a.name AS Name,value as Comment FROM sysobjects a left JOIN  (select * from  sys.extended_properties where minor_id =0) b ON a.id=b.major_id where a.xtype=\'U\' and a.name<>\'sysdiagrams\'';
        $result = $this->db->query($sql);
        $tableArray = array();
        while ($row = $this->db->fetch($result)) {
            $tableArray[] = $row;
        }
        return $tableArray;
    }

    public function entityCreate()
    {
        $fieldArray = $this->getTableField();
        $entityFileName = $this->getEntityFileName();
        $date = date("Y-m-d H:i:s");
        if ($fieldArray) {
            $fileContent = '<?php

/*
 * Tmac PHP MVC framework
 * $Author: zhangwentao $ 
 */

/**
 * Description of ' . $entityFileName['name'] . '
 *
 * @author Tracy McGrady
 */
class entity_' . $entityFileName['classname'] . '_base
{
';
            foreach ($fieldArray AS $field) {
                $fileContent.='    public $' . $field . ';';
                $fileContent .= "\r\n";
            }
            $fileContent .= '}';
            file_put_contents($entityFileName['file'], $fileContent, LOCK_EX);
            echo $entityFileName['file'] . '生成成功<br>';
        }
    }

    /**
     * 生成实体时用来取表里的字段名（仅能取到字段名）
     * @return type
     */
    private function getTableField()
    {
        $result = $this->db->query("SELECT TOP 1 * FROM {$this->getTable_name()}");
        $fieldArray = array();
        if ($result) {
            while ($fields = mssql_fetch_field($result)) {
                $fieldArray[] = $fields->name;
            }
        }
        return $fieldArray;
    }

    private function getEntityFileName()
    {
        $file_name_array = explode('_', $this->getTable_name());
        $file_name = '';
        foreach ($file_name_array AS $k => $v) {
            if ($k == 0 && $v == $this->getDb_prefix()) {
                continue;
            }
            $file_name.=ucwords($v);
        }
        $file = $this->entity_class_dir . $file_name . '.class.php';
        return array('file' => $file, 'name' => $file_name . '.class.php', 'classname' => $file_name);
    }

    public function getTableInfo()
    {
        $sql = "select sys.columns.name as field, sys.types.name, sys.columns.max_length, sys.columns.is_nullable, 
  (select count(*) from sys.identity_columns where sys.identity_columns.object_id = sys.columns.object_id and sys.columns.column_id = sys.identity_columns.column_id) as is_identity ,
  (select value from sys.extended_properties where sys.extended_properties.major_id = sys.columns.object_id and sys.extended_properties.minor_id = sys.columns.column_id) as description
  from sys.columns, sys.tables, sys.types where sys.columns.object_id = sys.tables.object_id and sys.columns.system_type_id=sys.types.system_type_id and sys.tables.name='{$this->getTable_name()}' order by sys.columns.column_id";
        $result = $this->db->getAll($sql);
        return $result;
    }

}
