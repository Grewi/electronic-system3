<?php

$function_directories = is_array(FUNCTION_DIRECTORIES)?FUNCTION_DIRECTORIES:[];

foreach($function_directories as $dir){
    if (file_exists($dir)) {
        $systemFnctionFiles = scandir($dir);
        if (is_iterable($systemFnctionFiles)) {
            foreach ($systemFnctionFiles as $file) {
                if (!file_exists($dir . '/' . $file)) {
                    continue;
                }
                $f = pathinfo($file);
                if ($f['extension'] == 'php') {
                    require $dir . '/' . $file;
                }
            }
        }
    }
}
