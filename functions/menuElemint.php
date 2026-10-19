<?php 

use system\core\app\app;

/**
* Функция определения активного меню
* Для использования динамичного элемента используйте знак вопроса
* Пример: /user/?/client 
*/
function menuElement(array $params): bool {
    $app = app::app();
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

