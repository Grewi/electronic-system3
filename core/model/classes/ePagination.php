<?php
namespace system\core\model\classes;

class ePagination
{
    private string $id;
    private int $limit = 20;
    private int $str = 1;
    private int $count;

    private array $lines = [];
    private int $priv = 0;
    private int $next = 0;
    private int $active = 0;
    private int $countPages = 0;


    public function id(string $id): void
    {
        $this->id = $id;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function str(): void
    {
        if (isset($_GET['str']) && is_numeric($_GET['str']) && $_GET['str'] > 0) {
            $this->str = (int) $_GET['str'];
        }
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function getPriv(): int
    {
        return $this->priv;
    }

    public function getNext(): int
    {
        return $this->next;
    }   
    
    public function getActive(): int
    {
        return $this->active;
    } 
    
    public function getStr(): int
    {
        return $this->str;
    }

    public function calcOffset(): int
    {
        return (($this->str * $this->limit) - $this->limit);
    }

    public function countPages(): int
    {
        return $this->countPages;
    }

    public function pagin(): void
    {
        $this->countPages = ceil($this->count / $this->limit); // Количество страниц
        if ($this->countPages > 1) {
            $r = [];
            for ($i = 1; $i <= $this->countPages; $i++) {
                $r[$i] = $i == $this->str ? 'active' : '';
            }
            $this->lines = $r;
        }

        if ($this->str > 1) {
            $this->priv = $this->str - 1;
        }

        if ($this->str < $this->countPages) {
            $this->next = $this->str + 1;
        }
        $this->active = $this->str;
    }
}