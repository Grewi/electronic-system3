<?php 

namespace system\core\valid\database;

use system\core\valid\item;
use system\core\database\database;

class valid_unique extends item 
{
    protected string $textError = 'Эти данные уже зарегистрированы в системе';
    private string $table;
    private string $col;
    private int $id;

    public function __construct(string $table, string $col, int $id = 0)
    {
        $this->table = $table;
        $this->col = $col;
        $this->id = $id;
    }

    public function control()
    {
        if ($this->original) {
            $db = database::connect();
            $i = $db->fetch(
                'SELECT COUNT(*) as `count`  FROM `' . $this->table . '` WHERE `' . $this->col . '` = :data AND id != :id', 
                [
                    'data'  => $this->original, 
                    'id' => $this->id],
            );
            if($i->count > 0){
                $this->setError($this->textError);
                $this->setControl(false);                 
            }
        }
    }    
}