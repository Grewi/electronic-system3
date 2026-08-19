<?php

namespace system\core\valid;

use system\core\valid\validInterface;

abstract class item implements validInterface
{
    protected bool $control = true;
    protected array $errors = [];
    protected mixed $original = null;
    protected mixed $result = null;
    protected string $textError = '';

    /**
     * @var bool getResult 
     */
    public bool $getResult = true;

    public function setData(array $data): void
    {
        $this->setControl($data['control']);
        array_merge($this->errors, $data['errors']);
    }

    /**
     * @param bool $control 
     * @return static 
     */
    public function setControl(bool $control): static
    {
        $this->control = ($this->control == true ? $control : false);
        if (!$this->control) {
            // $this->setError($this->textError);
        }

        return $this;
    }

    /**
     * @return bool 
     */
    public function getControl(): bool
    {
        return $this->control;
    }

    /**
     * @param string $error 
     * @return static 
     */
    public function setError(string $error): static
    {
        $this->errors[0] = $error;
        return $this;
    }

    /**
     * @param string $error 
     */
    public function addError(string $error): static
    {
        $this->errors[] = $error;
        return $this;
    }

    /**
     * @return array 
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string 
     */
    public function getError(): string
    {
        return implode(', ', $this->errors);
    }

    /**
     * @param mixed $original 
     * @return static 
     */
    public function setOriginal(mixed $original): static
    {
        $this->original = $original;
        return $this;
    }

    /**
     * @return mixed 
     */
    public function getOriginal(): mixed
    {
        return $this->original;
    }

    /**
     * @param mixed $original 
     * @return static 
     */
    public function setResulr(mixed $original): static
    {
        $this->original = $original;
        return $this;
    }

    /**
     * @return mixed 
     */
    public function getResult(): mixed
    {
        return $this->original;
    }

    /**
     * @param string $text 
     * @return static
     */
    public function setErrorText(string $text): static
    {
        $this->textError = $text;
        return $this;
    }

    public function control() {}
}

