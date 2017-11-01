dkj
===

Сайт Дворца Культуры Железнодорожников, v 1.3.0

Порядок установки проекта
1) выполнить команду `git clone git@github.com:paulfcdd/dkj96.ru.git` либо же
распаковать zip-архив в web-директорию сайта
2) выполнить последовательно команды  `cd dkj96.ru` и `composer install` (если Композер установлен глобально)
или же установить композер (https://getcomposer.org/download/) и выполнить `php composer.phar install`.
В процессе установки необходимо ввести все необходимые для подключения к БД данные 
3) Импортировать продукционную базу данных на свой сервер либо, если нужна чистая,
выполнить последовательно команды `php bin/console doctrine:database:create` и `php bin/console doctrine:schema:update --force` 
4) (Этот шаг нужен, если база данных была создана командами из пункта выше)
Для создания пользователя с правами админа выполнить команду
`php bin/console fos:user:create %username% --super-admin` и следовать инструкциям в терминале 
5) выполнить команды `cd web/` и `npm install` для установки всех зависимостей NPM 
(Bootstrap, jQuery etc)
6) в конце установки вернуться в корень проекта и выполнить команду `php bin/console cache:clear --no-warmup`
если вы в режиме разработчика или `php bin/console cache:clear --env=prod` для режима продукции
7) Для того, чтобы зайти в режим разработки и получить доступ ко всем инструментам разработчика
необходимо ввести URL следующего вида:
- `localhost/dkj96.ru/web/app_dev.php` (если не настроен VirtualHost)
- `dkj96.dev/app_dev.php` (если VirtualHost сконфигурирован)
Для пежима продукции:
- `localhost/dkj96.ru/web` (если не настроен VirtualHost)
- `dkj96.dev` (если VirtualHost сконфигурирован)

В случае вопросов, подробная документация доступна на https://symfony.com/doc/current/index.html