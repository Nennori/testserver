Необходимые ресурсы: docker, docker-compose
Для запуска сервера перейдите в директорию с проектом и введите
в терминал команду:
docker-compose up 
Для запуска сервера в фоновом режиме:
docker-compose up -d
Для генерации документации введите команду:
docker-compose exec testserver php artisan l5-swagger:generate
Посмотреть документацию можно по адресу http:\\localhost:8000\api\documentation
Для завершения работы сервера:
Ctrl+c
Для завершения работы сервера, запущенного в фоновом режиме:
docker-compose down

