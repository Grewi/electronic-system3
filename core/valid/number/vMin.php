<?php 

namespace system\core\valid\number;

use system\core\valid\item;

class vMin extends item
{
    private string $regex = "/^[0-9\.\,-]+$/u";
    protected string $textError;

    public bool $getResult = false;

    protected float|int $param;

    public function __construct(float|int $param)
    {
        $this->param = $param;
        $this->textError = 'Значение не должно быть меньше ' . $param;
    }

    public function control()
    {
        if ($this->original && $this->original < $this->param) {
            $this->setError($this->textError);
            $this->setControl(false);
        }
    }
}