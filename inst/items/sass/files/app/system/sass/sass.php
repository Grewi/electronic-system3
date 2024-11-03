<?php 
namespace electronic\sass;

class sass extends \system\core\sass\sass
{
    public $mini = false;
    protected $url = 'https://github.com/sass/dart-sass/releases/download/1.32.13/';
    protected $file = [
        'linux-ia32'       => 'dart-sass-1.32.13-linux-ia32.tar.gz',
        'linux-x64'        => 'dart-sass-1.32.13-linux-x64.tar.gz',
        'windows-ia32'     => 'dart-sass-1.32.13-windows-ia32.zip',
        'windows-x64'      => 'dart-sass-1.32.13-windows-x64.zip',
    ];

    // Путь к scss файлам 'имя' => 'путь'
    public $input = [
        'style'     => '/composer/vendor/twbs/bootstrap/scss/bootstrap.scss',
    ];

    // Путь к css файлам 'имя' => 'путь'
    public $output = [
        'style'     => '/public/assets/css/style.css',
    ];

    // Список имён, которые будут компилироваться в автоматическом режиме php e style
    public $list = [
        'style'
    ];
}