<?php 
namespace system\core\model\classes;

use system\core\config\config;
use system\core\database\database;

class insert
{
    private $bind;
    public function insert(array|object $data, string $table, array $bind, string $databaseName = null)
    {
        $db = database::connect($databaseName);
        $count = count($data);
        $strKey = '';
        $strData = '';
        $c = 0;
        foreach($data as $key => $i){
            $c++;
            $strKey .= ' `' . $key . '`' . ($c == $count ? ' ' : ', ');
            $strData .= ' :' . $key . '' . ($c == $count ? ' ' : ', ');
        }

        $data = array_merge($data, $bind);
        $sql = 'INSERT INTO ' . $table . ' (' . $strKey .') VALUES (' . $strData . ')';
        $db->query($sql, $data);
        try{

            if (config::database('type') == 'sqlite') {
                $dbId = $db->fetch('SELECT Last_insert_rowid() as ' . $model->_id, []);
            }else{
                $dbId = $db->fetch('SELECT * FROM ' . $table . ' where ' . $model->_id .' = LAST_INSERT_ID()', []);
            }

            $ob = static::class;
            $result = $ob::find($dbId->{$model->_id});
            return $result ? $result : null;
        }catch(\Exception $e){
            return null;
        }
        
    }
}