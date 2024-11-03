<?php 
namespace system\inst\items\sass;
use system\core\app\app;
use system\inst\classes\itemIndex;
use system\inst\classes\functions;

class index implements itemIndex
{
    public function params() : void
    {

    }

    public function files() : void
    {

    }

    public function database() :void
    {

    }

    public function finish() :void
    {
        $a = null;
        while ($a === null) {
            echo "Запустить composer require twbs/bootstrap:5.0.2? (yes/no): ";
            $a = functions::yes(trim(fgets(STDIN)));
        }
        if ($a) {
            exec('cd ' . ROOT . '/composer && php ' . ROOT . '/composer/composer.phar require twbs/bootstrap:5.0.2');
        }else{
            echo "Вы можете запустить установу позже вручную";
        }
    }    
}