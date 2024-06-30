<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Kanastra

## Instalar projeto

* Clone o repositorio
* Clone o arquivo .env.example para .env
* Execute o seguite comando: 
  *     docker compose up --build -d
* Após o build acesse o docker "kanastra-app" de acordo com o teu sistema
    *     docker exec -it kanastra-app bash

## Comandos outros
Execute os comandos seguintes comandos na ordem

*     composer install
*     php artisan optimize
*     php artisan migrate
*     php artisan migrate --env=testing
*     php artisan horizon

Caso precise limpar a fila rode

*     php artisan redis:flush

Para rodar os test
*     php artisan test
