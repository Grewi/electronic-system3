<?php

namespace system\core\valid;

use Closure;
use system\core\valid\item;
use system\inst\classes\functions;

class valid
{
    private bool $control = true;

    /**
     * control
     * errors
     * original
     * result
     */
    private array $data = [];

    /**
     * Массив изначальных значений key => value
     */
    private array $original = [];

    public function add(string $name, item $item, callable|null $function = null)
    {
        if (!isset($this->data[$name])) {
            // return;
        }

        if (isset($this->original[$name])) {
            $item->setOriginal($this->original[$name]);
        }
        if ($function) {
            $function($item);
        }
        $item->control();
        $this->data[$name]['control'] = $item->getControl();
        $this->data[$name]['errors'] = $item->getErrors();
        $this->data[$name]['original'] = $item->getOriginal();
        if ($item->getResult || !isset($this->data[$name])) {
            $this->data[$name]['result'] = $item->getResult();
        }

        $this->setControl($item->getControl());
    }

    public function error(string $name): array
    {
        return isset($this->data[$name]) ? $this->data[$name]['errors'] : [];
    }

    public function errors(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors'])) {
                $errors[$a] = $i['errors'];
            }
        }
        return $errors;
    }

    public function errorList(): array
    {
        $errors = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['errors']) && count($i['errors']) > 0) {
                foreach ($i['errors'] as $er) {
                    $errors[] = $er;
                }
            }
        }
        return $errors;
    }

    public function original(string $name):mixed
    {
        return (isset($this->data[$name]['original']) ? $this->data[$name]['original'] : null);
    }

    public function originals(): array
    {
        $original = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['original'])) {
                $original[$a] = $i['original'];
            }
        }
        return $original;
    }

    public function results(): array
    {
        $original = [];
        foreach ($this->data as $a => $i) {
            if (isset($i['result'])) {
                $original[$a] = $i['result'];
            }
        }
        return $original;
    }

    public function result(string $name):mixed
    {
        return (isset($this->data[$name]['result']) ? $this->data[$name]['result'] : null);
    }    

    public function control(): bool
    {
        return $this->control;
    }

    public function setOriginalArray(array $data)
    {
        $this->original = $data;
    }

    public function setRequest(): void
    {
        $this->original = $_REQUEST;
    }

    private function setControl(bool $control)
    {
        if ($this->control) {
            $this->control = $control;
        }
    }
}