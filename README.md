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