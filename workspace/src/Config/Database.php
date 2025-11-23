<?php
namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public static function init()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'pgsql',
            'host' => getenv('POSTGRES_HOST') ?: 'postgres',
            'port' => getenv('POSTGRES_PORT') ?: '5432',
            'database' => getenv('POSTGRES_DB'),
            'username' => getenv('POSTGRES_USER'),
            'password' => getenv('POSTGRES_PASSWORD'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}