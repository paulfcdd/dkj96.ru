
Сайт Дворца Культуры Железнодорожников
=======
Порядок установки проекта
1) выполнить команду `git clone git@github.com:paulfcdd/dkj96.ru.git` либо же
распаковать zip-архив в web-директорию сайта

2) Зайти в директорию проекта и запустить файл `install.sh`. 
В процессе установки понадобится ввести данные для соединения с базой даннх

3) После завершения установки, для того, чтобы зайти в режим разработки и получить доступ ко всем инструментам разработчика
необходимо ввести URL следующего вида:
- `localhost/dkj96.ru/web/app_dev.php` (если не настроен VirtualHost)
- `dkj96.dev/app_dev.php` (если VirtualHost сконфигурирован)

Для режима продукции:
- `localhost/dkj96.ru/web` (если не настроен VirtualHost)
- `dkj96.dev` (если VirtualHost сконфигурирован)

В случае вопросов, подробная документация доступна на https://symfony.com/doc/current/index.html

