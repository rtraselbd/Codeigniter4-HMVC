<?php

namespace Config;

use Illuminate\Database\Capsule\Manager as Capsule;
use Config\Tenancy;

class Eloquent extends Tenancy
{
    protected $driver;

    public function __construct()
    {
        parent::__construct();

        $capsule = new Capsule;

        switch (config('Database')->default['DBDriver']) {
            case 'MySQLi':
                $this->driver = 'mysql';
                break;
            case 'Postgre':
                $this->driver = 'pgsql';
                break;
            case 'SQLite3':
                $this->driver = 'sqlite';
                break;
            case 'SQLSRV':
                $this->driver = 'sqlsrv';
                break;
            default:
                $this->driver = 'mysql';
                break;
        }

        $capsule->addConnection([
            'driver'    => $this->driver,
            'host'      => config('Database')->default['hostname'],
            'port'      => config('Database')->default['port'],
            'database'  => config('Database')->default['database'],
            'username'  => config('Database')->default['username'],
            'password'  => config('Database')->default['password'],
            'charset'   => config('Database')->default['charset'],
            'collation' => config('Database')->default['DBCollat'],
            'prefix'    => config('Database')->default['DBPrefix'],
            'strict'    => config('Database')->default['strictOn'],
            'schema'    => config('Database')->connect()->schema ?? 'public'
        ]);

        $capsule->setAsGlobal();

        $capsule->bootEloquent();
    }
}
