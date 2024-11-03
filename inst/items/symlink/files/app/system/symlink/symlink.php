<?php 
namespace electronic\symlink;

class symlink extends \system\core\symlink\symlink
{
    public array $list = [
        [
            'target' => '/composer/vendor/twbs/bootstrap',
            'link' => '/public/adm/bootstrap',
        ],
    ];
}