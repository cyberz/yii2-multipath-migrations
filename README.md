Yii2 Multiple Path Migration
============================
Allow to apply migrations from multiple paths or aliases

Installation
------------
via Composer:
```
php composer.phar require cyberz/yii2-multipath-migrations ">=1.0.0"
```

Usage
-----
In your console application configuration add:

```php
'controllerMap' => [
        'migrate' => [
            'class' => 'cyberz\migrations\controllers\MigrationsController',
            'migrationLookup' => [
                '@app',                         // will lookup in .../migrations/migration_name.php AND .../<some-dir-name>/migrations/migration_name.php
                '@backend',                     // will lookup in .../backend/<some-dir-name>/migrations/migration_name.php AND .../backend/modules/<some-dir-name>/migrations/migration_name.php 
                '@frontend/modules',            // will lookup in .../frontend/modules/<some-dir-name>/migrations/migration_name.php
                '@app/some/path/to/migrations', // will lookup in .../some/path/to/migrations/migration_name.php
                '@app/some/*/to/*/migrations',  // will lookup in .../some/<some-dir-name>/to/<some-dir-name>/migrations
            ],
        ],
    ],
```