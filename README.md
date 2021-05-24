To Do Testeserver
=================
A Laravel app that serves as backend for To Do application
##Quick start
>Prerequisites: docker, docker-compose
>  
To run server to the root project directory and enter the command:
* Run server
  
        docker-compose up
* Run server in background mode

        docker-compose up -d
To generate documentation

        docker-compose exec testserver php artisan l5-swagger:generate

To view documentation go to http:\\localhost:8000\api\documentation

To shut down docker server

        Ctrl+c

To shut down docker server in background mode

        docker-compose down

