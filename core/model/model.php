<?php
namespace system\core\model;

use system\core\database\database;
use system\core\collection\collection;
use system\core\model\iteratorDataModel;
use system\core\model\traits\wrap;
use system\core\model\classes\{
    eSelect,
    eFrom,
    eSort,
    eBind,
    eWhere,
    eLimit,
    eJoin,
    eInsert,
    eUpdate,
    eDelete,
    eOffset,
    eGroup,
    ePagination,
};

class model extends iteratorDataModel implements \JsonSerializable
{
    private collection $EMD;

    use wrap;

    public function __construct()
    {
        $this->EMD = new collection;
        $this->EMD->databaseName = 'database';
        $this->EMD->select = new eSelect;
        $this->EMD->from = new eFrom;
        $this->EMD->sort = new eSort;
        $this->EMD->bind = new eBind;
        $this->EMD->where = new eWhere;
        $this->EMD->limit = new eLimit;
        $this->EMD->join = new eJoin;
        $this->EMD->group = new eGroup;
        $this->EMD->insert = new eInsert;
        $this->EMD->update = new eUpdate;
        $this->EMD->delete = new eDelete;
        $this->EMD->offset = new eOffset;
        $this->EMD->pagination = new ePagination;
        $c = explode('\\', get_called_class());
        $this->EMD->from->add(array_pop($c));
        $this->EMD->id = 'id';
    }

    /**
     * Значение поля SELECT. по умолчанию "*" 
     * @param string $select
     * @return model
     */
    public function select(string $select)
    {
        $this->EMD->select->add($select);
        return $this;
    }

    /**
     * Возвращать id после update и insert
     */
    public function returnedId()
    {
        return true;
    }

    /**
     * Значение поля FROM. По умолчанию наименование класса
     * @param string $from
     * @return model
     */
    public function from(string $from): static
    {
        $this->EMD->from->add($from);
        return $this;
    }

    /**
     * Устанавливает условный оператор для функций where
     */
    public function or(): static
    {
        $this->EMD->where->or();
        return $this;
    }

    /**
     * Условие с указанием оператора 
     * @param string $col Поле таблицы в базе
     * @param string $operator Оператор для условия (= < <= > >= !=)
     * @param string|int|float $value Значение поля
     * @param bool $or Логический оператор с предыдущим блоком where. По умолчанию "AND"
     * @return model
     */
    public function whereL(string $col, string $operator, string|int|float $value, bool $or = false): static
    {
        $this->EMD->where->where($col, $operator, $value, $or);
        return $this;
    }

    /**
     * Короткое условие. В качестве оператора значение "="
     * @param string $col Поле таблицы в базе
     * @param string|int|float $value Значение поля
     * @param bool $or Логический оператор с предыдущим блоком where. По умолчанию "AND"
     * @return model
     */
    public function where(string $col, string|int|float $value, bool $or = false): static
    {
        $this->EMD->where->where($col, '=', $value, $or);
        return $this;
    }

    /**
     * Условие соответствия поля значению NULL
     * @param string $col Поле таблицы в базе
     * @return model
     */
    public function whereNull(string $col): static
    {
        $this->EMD->where->whereNull($col);
        return $this;
    }

    /**
     * Условие не соответствия поля значению NULL
     * @param string $col Поле таблицы в базе
     * @return model
     */
    public function whereNotNull(string $col): static
    {
        $this->EMD->where->whereNotNull($col);
        return $this;
    }

    /**
     * Условие соответствия поля значениям в массиве 
     * @param string $col Поле таблицы в базе
     * @param array|object $arg Список возможных значений
     * @return model
     */
    public function whereIn(string $col, array|object $arg): static
    {
        $this->EMD->where->whereIn($col, $arg);
        return $this;
    }

    /**
     * Условие соответствия поля значениям в массиве 
     * @param string $col Поле таблицы в базе
     * @param array|object $arg Список возможных значений
     * @return model
     */
    public function whereLike(string $col, string $arg): static
    {
        $this->EMD->where->whereLike($col, $arg);
        return $this;
    }

    /**
     * Условие соответствия поля значениям в массиве 
     * @param string $col Поле таблицы в базе
     * @param array|object $arg Список возможных значений
     * @return model
     */
    public function whereLikeStart(string $col, string $arg): static
    {
        $this->EMD->where->whereLikeStart($col, $arg);
        return $this;
    }

    /**
     * Условие соответствия поля значениям в массиве 
     * @param string $col Поле таблицы в базе
     * @param array|object $arg Список возможных значений
     * @return model
     */
    public function whereLikeEnd(string $col, string $arg): static
    {
        $this->EMD->where->whereLikeEnd($col, $arg);
        return $this;
    }

    /**
     * Произвольная строка для условия
     * @param string $str Строка
     * @param array $bind Данные для биндинга
     * @return model
     */
    public function whereStr(string $str, array $bind = [])
    {
        $this->EMD->where->whereStr($str, $bind);
        return $this;
    }

    /**
     * Значение поля LIMIT
     * @param int $limit
     * @return model
     */
    public function limit(int $limit): static
    {
        $this->EMD->limit->add($limit);
        return $this;
    }

    /**
     * Сортировка по полю (полям)
     * @param string $name Поле таблицы в базе
     * @param string $type Направление сортировки
     * @return model
     */
    public function sort(string $name, string $type = 'asc'): static
    {
        $this->EMD->sort->add($name, $type);
        return $this;
    }

    public function group(string $group): static
    {
        $this->EMD->group->add($group);
        return $this;
    }

    /**
     * Объединение  INNER
     * @param string $tableName Наименование таблицы для объединения
     * @param string $firstTable Поле текущей таблицы для сравненеи
     * @param string $secondaryTable Поле подключаемой таблицы для сравнения
     * @return model
     */
    public function innerJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->EMD->join->join($tableName, $firstTable, $secondaryTable, 0);
        return $this;
    }

    /**
     * Объединение  LEFT
     * @param string $tableName Наименование таблицы для объединения
     * @param string $firstTable Поле текущей таблицы для сравненеи
     * @param string $secondaryTable Поле подключаемой таблицы для сравнения
     * @return model
     */
    public function leftJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->EMD->join->join($tableName, $firstTable, $secondaryTable, 1);
        return $this;
    }

    /**
     * Объединение  RIGHT
     * @param string $tableName Наименование таблицы для объединения
     * @param string $firstTable Поле текущей таблицы для сравненеи
     * @param string $secondaryTable Поле подключаемой таблицы для сравнения
     * @return model
     */
    public function rightJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->EMD->join->join($tableName, $firstTable, $secondaryTable, 2);
        return $this;
    }

    /**
     * Объединение  FULL
     * @param string $tableName Наименование таблицы для объединения
     * @param string $firstTable Поле текущей таблицы для сравненеи
     * @param string $secondaryTable Поле подключаемой таблицы для сравнения
     * @return model
     */
    public function fullJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->EMD->join->join($tableName, $firstTable, $secondaryTable, 3);
        return $this;
    }

    /**
     * Объединение  CROSS
     * @param string $tableName Наименование таблицы для объединения
     * @param string $firstTable Поле текущей таблицы для сравненеи
     * @param string $secondaryTable Поле подключаемой таблицы для сравнения
     * @return model
     */
    public function crossJoin(string $tableName, string $firstTable, string $secondaryTable): static
    {
        $this->EMD->join->join($tableName, $firstTable, $secondaryTable, 4);
        return $this;
    }

    /**
     * Зоздаёт новую запись в базе данных
     * @param array $data Данные для записи. Ключи массива должны соответствовать поля в базе
     * @return model
     */
    public function insert(array $data = []): ?int
    {
        // $d = array_merge(get_object_vars($this), $data);
        $this->addPropertyModel($data);
        $this->EMD->insert->databaseName($this->EMD->databaseName);
        $this->EMD->insert->table($this->EMD->from->get());
        $this->EMD->insert->bind($this->bind());
        $this->EMD->insert->id($this->EMD->id);
        $this->EMD->insert->data($this->getPropertyModel());
        return $this->EMD->insert->save();
    }

    /**
     * Изменения в записи. Возвращает количество изменённых данных 
     * @param array $data
     * @return int
     */
    public function update(array $data = []): int
    {
        // $d = array_merge(get_object_vars($this), $data);
        $this->addPropertyModel($data);
        if($this->id){
            $this->where($this->EMD->id, $this->{$this->EMD->id});
        }
        $this->EMD->update->where($this->EMD->where->get());
        $this->EMD->update->databaseName($this->EMD->databaseName);
        $this->EMD->update->table($this->EMD->from->get());
        $this->EMD->update->bind($this->bind());
        $this->EMD->update->id($this->EMD->id);
        $this->EMD->update->data($this->getPropertyModel());
        return $this->EMD->update->save();
    }

    /**
     * Сохраняет и возвращает объект модели
     * @return static
     */
    public function save(array $data = []): static
    {
        $this->update($data);
        return $this->find($this->id);
    }

    /**
     * Создаёт запись и возвращает объект модели
     * @return static
     */
    public function create(array $data = []): static
    {
        $id = $this->insert($data);
        return $this->find((int)$id);
    }

    /**
     * Удаление записи. Возвращает количество удалённых строк
     * @param array $data
     * @return void
     */
    public function delete(array $data = []): int
    {
        // $d = array_merge(get_object_vars($this), $data);
        $this->addPropertyModel($data);
        if ($this->id) {
            $this->where($this->EMD->id, $this->{$this->EMD->id});
        }
        $this->EMD->delete->where($this->EMD->where->get());
        $this->EMD->delete->databaseName($this->EMD->databaseName);
        $this->EMD->delete->table($this->EMD->from->get());
        $this->EMD->delete->id($this->EMD->id);
        $this->EMD->delete->data($this->bind());
        return $this->EMD->delete->save();
    }

    /**
     * 
     */
    public function pagin(int $limit = 20): static
    {
        $this->EMD->pagination->str();
        $this->EMD->pagination->setLimit($limit <= 0 ? 20 : $limit);
        $this->EMD->offset->add($this->EMD->pagination->calcOffset());
        $this->EMD->limit->add($this->EMD->pagination->getLimit());
        $this->EMD->pagination->setCount($this->count());
        $this->EMD->pagination->pagin();
        return $this;
    }

    public function pagination(string $url = ''): array
    {
        return [
            'lines' => $this->EMD->pagination->getLines(),
            'priv' => $this->EMD->pagination->getPriv(),
            'next' => $this->EMD->pagination->getNext(),
            'active' => $this->EMD->pagination->getActive(),
            'pages' => $this->EMD->pagination->countPages(),
            'url' => $url,
        ];
    }

    /**
     * Обработка get параметров sort 
     * @param array $listCol - Список колонок по которым допускается сортировка
     * @param string $defaultSort - стролбец по умолчанию
     * @param string $defaultDirection - направление по умолчанию
     * @return mixed
     */
    public function sorting(array $listCol = [], $defaultSort = '', $defaultDirection = ''): static
    {
        if (!isset($_GET['sort']) || !in_array($_GET['sort'], $listCol)) {
            if (!empty($defaultSort) && !empty($defaultDirection)) {
                $this->sort($defaultSort, $defaultDirection);
            }
            return $this;
        }
        $direction = (isset($_GET['direction']) && $_GET['direction'] == 'desc') ? 'desc' : 'asc';
        $sort = $_GET['sort'];
        $this->sort($sort, $direction);
        return $this;
    }

    /**
     * Обработка get параметров filter_* Поиск совпадений в столбце по запросу
     * @param string $name - наименование get параметра 
     * @param string $col - наименование столбца в таблице, если не указан, то равен параметру name
     * @return mixed
     */
    public function filterLike(string $name, ?string $col): static
    {
        $col = $col ? $col : $name;
        if (isset($_GET['filter_' . $name]) && $_GET['filter_' . $name] != '') {
            $this->whereLike($col, $_GET['filter_' . $name]);
        }
        return $this;
    }

    /**
     * Обработка массива get параметров filter_* Поиск совпадений в нескольких столбцах по запросу
     * @param string $name - наименование get параметра 
     * @param string $col - Перечисление столбцов для поиска
     * @return static
     */
    public function filterLikeMulty(string $name, array $cols): static
    {
        if (isset($_GET['filter_' . $name]) && $_GET['filter_' . $name] != '') {
            $r = ' (';
            $c = 0;
            $count = 0;
            $bind = [];
            foreach ($cols as $col) {
                ++$c;
                $col = $col ? $col : $name;
                $colb = str_replace('.', '_', $col) . '_f' . ++$count;
                $bind[$colb] = $_GET['filter_' . $name];
                $r .= ' ' . $this->wrap($col) . ' LIKE CONCAT("%", :' . $colb . ',"%") ';
                if ($c < count($cols)) {
                    $r .= ' OR ';
                }
            }
            $r .= ') ';
            $this->whereStr($r, $bind);
        }
        return $this;
    }

    /**
     * Обработка get параметров filter_*[min] и filter_*[max] для обработки диапазона параметров
     * @param string $name - наименование столбца в таблице
     * @return static
     */
    public function filterRange(string $name): static
    {
        if (isset($_GET['filter_' . $name])) {

            if (isset($_GET['filter_' . $name]['min']) && !empty($_GET['filter_' . $name]['min'])) {
                $this->whereL($name, '>=', $_GET['filter_' . $name]['min']);
            }

            if (isset($_GET['filter_' . $name]['max']) && !empty($_GET['filter_' . $name]['max'])) {
                $this->whereL($name, '<=', $_GET['filter_' . $name]['max']);
            }
        }
        return $this;
    }

    /**
     * Массив данных 
     * @return array
     */
    public function all(): array
    {
        $db = database::connect($this->EMD->databaseName);
        return $db->fetchAll($this->slectSql(), $this->bind(), get_class($this));
    }

    /**
     * Единичная запись
     * @return model
     */
    public function get()
    {
        $db = database::connect($this->EMD->databaseName);
        return $db->fetch($this->slectSql(), $this->bind(), get_class($this));
    }

    /**
     * Запись соответствубщая идентификатору
     * @param int $id
     */
    public function find(mixed $id): ?static
    {
        $d = (int) $id;
        $db = database::connect($this->EMD->databaseName);
        return $db->fetch('SELECT * FROM ' . $this->EMD->from->get() . ' WHERE `' . $this->EMD->id . '` = :' . $this->EMD->id . ' ', [$this->EMD->id => $id], get_class($this));
    }

    public function search(string $col, int|float|string $value): static
    {
        $db = database::connect($this->EMD->databaseName);
        return $db->fetch('SELECT * FROM ' . $this->EMD->from->get() . ' WHERE `' . $col . '` = :' . $col . ' ', [$col => $value], get_class($this));
    }

    /**
     * Количество записей
     * @return int
     */
    public function count(): int
    {
        $str = 'SELECT COUNT(*) as `count` FROM ' .
            $this->EMD->from->get() . ' ' .
            $this->EMD->join->get() . ' ' .
            $this->EMD->where->get() . ' ' .
            $this->EMD->group->get();
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return (int) db($this->EMD->databaseName)->fetch($str, $this->bind(), get_class($this))->count;
    }

    /**
     * Сумма 
     * @param mixed $name Наименование поля для суиммирования
     * @return float
     */
    public function summ($name): float
    {
        $str = 'SELECT SUM(' . $this->wrap($name) . ') as `summ` FROM ' .
            $this->EMD->from->get() . ' ' .
            $this->EMD->join->get() . ' ' .
            $this->EMD->where->get() . ' ' .
            $this->EMD->group->get();
        $str = preg_replace('/\s{2,}/', ' ', $str);
        return (float) db($this->EMD->databaseName)->fetch($str, $this->bind(), get_class($this))->summ;
    }

    /**
     * Возвращает массив значений указанного столбца
     * [1, 2, 3, ...]
     * @param string $col - Наименование столбца
     * @return array
     */
    public function toArray(string $col = 'id'): array
    {
        $r = [];
        foreach($this->all() as $i){
            $r[] = $i->{$col};
        }
        return $r;
    }

    public function sqlPrint($format = true, $exit = false): void
    {
        if ($format) {
            dump($this->slectSql());
        } else {
            print_r($this->slectSql()) . PHP_EOL;
        }
        if ($exit) {
            exit();
        }
    }

    public function bindPrint($format = true, $exit = false): void
    {
        if ($format) {
            dump($this->bind());
        } else {
            print_r($this->bind()) . PHP_EOL;
        }
        if ($exit) {
            exit();
        }
    }

    private function slectSql(): string
    {
        $a = 'SELECT ' . $this->EMD->select->get() . ' ' . ' FROM ' .
            $this->EMD->from->get() . ' ' .
            $this->EMD->join->get() . ' ' .
            $this->EMD->where->get() . ' ' .
            $this->EMD->group->get() . ' ' .
            $this->EMD->sort->get() . ' ' .
            $this->EMD->limit->get() . ' ' .
            $this->EMD->offset->get();
        return preg_replace('/\s{2,}/', ' ', $a);
    }

    private function bind(): array
    {
        return array_merge($this->EMD->where->bind->get());
    }

    /**
     * Метод отвечает за вывод при серриализации объекта
     */
    #[\ReturnTypeWillChange] 
    public function jsonSerialize() {
        $array = [];
        foreach($this as $a => $i){
            $array[$a] = $i;
        }
        return $array;
    }
}
