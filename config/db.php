<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=jnoj',
    'username' => 'debian-sys-maint',
    'password' => 'Evo7JLxCBve0GgyW',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];
