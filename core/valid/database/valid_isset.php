<?php 

namespace system\core\valid\database;

use system\core\valid\item;
use system\core\database\database;

class valid_isset extends item 
{
    protected string $textError = 'Эти данных нет в системе';
    private string $table;
    private string $col;

    public function __construct(string $table, string $col = 'id')
    {
        $this->table = $table;
        $this->col = $col;
    }

    public function control()
    {
        if ($this->original) {
            $db = database::connect();
            $i = $db->fetch(
                'SELECT COUNT(*) as count FROM ' . $this->table . ' WHERE ' . $this->col . ' = :data', 
                ['data' => $this->original]
            );
            if($i->count <= 0){
                $this->setError($this->textError);
                $this->setControl(false);                 
            }
        }
    } 
}