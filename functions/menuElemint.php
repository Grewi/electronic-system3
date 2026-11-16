<?php 

use system\core\app\app;

/**
* Функция определения активного меню
* Для использования динамичного элемента используйте знак вопроса
* Пример: ['user', '?', 'client'] -> /user/1/client
* Строгий режим. Параметры должны точно соответствовать запросу. 
* Нпример ['user'] на странице /user/settings в обычном режиме вернёт true, в строгом false
*/
function menuElement(array $params, bool $strong = false): bool {
    $app = app::app();
    if($strong){
        return '/'.implode('/', $params) == $app->bootstrap->uri ? true : false;
    }
    if(empty($params) || count($app->request->params) < count($params)){
        return false;
    }

    foreach($params as $a => $i){
        if($i == '?' && $a+1 == count($params)){
            return true;
        }
        if($i == '?'){
            continue;
        }
        if(!isset($app->request->params[$a+1]) || $app->request->params[$a+1] != $i){
            return false;
        }
    }
    return true;
};

