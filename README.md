# How to use


<details>
  <summary>Run laravel in Docker</summary>
  
  1. Clone the repositore

        ```shell
        $ git clone git@github.com:alexyucra/lara_docker.git

        $ cd lara_docker

        $ cp app/.env.example app/.env
        ```

  2. Enter to container php

        ```shell
        docker exec -it php /bin/bash
        or
        docker exec -it php ls -la
        ```
     * Install composer

           ```shell
           $ docker exec -it php apt update -y && apt upgrade -y
     
           $ docker exec -it php php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" 
           
           $ docker exec -it php wget -O composer-setup.php https://getcomposer.org/installer
           
           $ docker exec -it php composer --version
           
           $ docker exec -it php composer install
           
           $ docker exec -it php composer update
           ```

     * Install npm

           ```shell
           $ docker exec -it php apt install nodejs npm -y
     
           $ docker exec -it php node -v
           ```
     *  run below command for generate keygen and config cache
       
           > docker exec -it php php artisan key:generate

           > docker exec -it php php artisan config:cache

</details>


