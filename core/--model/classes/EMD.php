<?php
namespace system\core\model\classes;
use system\core\model\classes\{eSelect, eFrom, eSort, eBind, eWhere, eLimit, eJoin, eInsert, eUpdate};

/**
 * Electronic Model Data
 */
#[\AllowDynamicProperties]
class EMD 
{
    public eSelect $select;
    public eFrom $from;
    public eWhere $where;
    public eBind $bind;
    public eSort $sort;  
    public eJoin $join;
    public eLimit $limit;
    public eInsert $insert;
    public eUpdate $update;

    /**
     * Наименование файла настроек с данными для подключения к базе данных "ROOT /app/configs/database.ini"
     * @var string
     */
    public $databaseName = 'database';

    /**
     * Наименование таблицы в базе данных, по умолчанию совпадает с именем класса модели
     * @var string
     */
    public $table = '';

    public $idNumber = 0;

    /**
     * Наименование поля с первичным ключём
     * @var string
     */
    public $id = 'id';

    public $paginCount = 20;
    public $limitDirection = 20;
    public $offset = '';
    public $group = '';
    public $paginationLine = [];
    public $paginationPriv = 0;
    public $paginationNext = 0;
    public $paginationActive = 0;

    public function select(): void
    {
        if(!in_array('select', get_object_vars($this))){
            $this->select = new eSelect();
        }
    }
}