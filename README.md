# Electronic-system
[Документация, примеры реализаций и т.д. на сайте](http://grewi.ru/blogs/category/electronic)

## Установка системы
Для установки системы необходимо скопировать файлы в директрию **system**, это можно сделать вручную или клонировав репозиторий git:
```bash
git clone git@github.com:Grewi/electronic-system.git ./system
```
Следующие комманды требуют наличие установленного интерпритатора php глобально. Если php запущен как контейнер docker, то необходимо зайти в контейнер с помощью комманды:
```bash
docker exec -it -u user-name contaner-name /bin/bash
```

## Ручная установка ##

Для запуска установщика необходимо вызвать комманду
```bash
php system/install 
```
Комманда выведет список доступных элементов для установки в ручном режиме:
```bash
Необходимо указать компонент установки.
Для установки доступны: 
    admin: Установка панели администрирования
    auth: Установка системы авторизации
    database: Файлы приложения для создания и настройки соединения с базой данных
    email: Настройка отправки электронной почты
    sass: Загрузка компилятора sass. 
    start: Первоначальная установка
    upload: Загрузка библиотеки verot/class.upload.php
```

Для каждого элемента достумен вызов справки
```bash
php system/install start help
```

Для установки необходимо элементов необходимо передавать требуемые параметры, в противном случае будут применены значения по умолчанию. Например:

```bash
php system/install start app=apps/app
```
## Автоматическая установка ##

Для автоматической установки требуется создать **install.ini** в корне проекта. 

```
[apps/app]
start.public = public
email.app = apps/app
upload.app = apps/app
sass.app = apps/app
database.type = mysql
database.name = electronic
database.user = docker
database.pass = docker
database.host = database
database.file =
auth.admin_login = admin
auth.admin_pass = 12345
auth.admin_email = admin@admin.ru
admin.public = public
```
В файле перечислены значения всех необходимых параметров

## Минимальная установка системы (ручной режим)
Для работы достаточно создать папку system в корне проекта и скопировать туда системные файлы или клонировать репозиторий
```bash
git clone git@github.com:Grewi/electronic-system.git ./system
```
Создать папку для доступа по http например public. Создать в ней файл index.php

```php
define('INDEX', true);
define('ENTRANSE', 'web');
require_once dirname(__DIR__) . '/system/system.php';
```
Необходимо направить все запросы на данный файл, за исключением запросов на существующие в этой директории файлы. 
Для сервера apache можно создать файл .htaccess
```bash
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule> 
```

Далее необходимо создать директрию приложения - app 
Общая структура должна выглядеть так:
```
app
  controllers
    indexController.php
  route
    web.php
public
  index.php
  .htaccess
system
```
Файл app/route/web.php
```php
use app\controllers\indexController;
use system\core\route\route;

$route = new route();

$route->controller(indexController::class, 'index');

exit('404');
```

Файл app/controllers/indexController.php
```php
namespace app\controllers;

class IndexController
{
    public function index()
    {
        var_dump('index');
    }
}
```