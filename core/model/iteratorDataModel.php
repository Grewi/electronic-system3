<?php

namespace system\core\model;

class iteratorDataModel implements \IteratorAggregate
{
    private array $iteratorDataModel;

    protected function addPropertyModel(array $properties): void
    {
        $this->iteratorDataModel = array_merge($this->iteratorDataModel, $properties);
    }

    protected function getPropertyModel(): array
    {
        return $this->iteratorDataModel;
    }

    public function __set($name, $value)
    {
        $this->iteratorDataModel[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->iteratorDataModel[$name])) {
            return $this->iteratorDataModel[$name];
        }
    }

    public function __debugInfo()
    {
        return $this->iteratorDataModel ?? null;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->iteratorDataModel);
    }
}
