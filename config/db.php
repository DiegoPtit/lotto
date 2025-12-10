<?php

return [
    'class' => 'yii\db\Connection',
    //'dsn' => 'mysql:host=localhost;dbname=rifas_db',
    //'username' => 'root',
    //'password' => '',
    'dsn' => 'mysql:host=mysql-lotto-lotto-project.j.aivencloud.com;port=27526;dbname=rifas_db',
    'username' => 'avnadmin',
    'password' => 'AVNS_ZwR0pERRomrl1MOzZEt',
    'charset' => 'utf8',
    'attributes' => [
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ],

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
