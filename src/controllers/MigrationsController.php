<?php

namespace cyberz\migrations\controllers;

use Yii;
use yii\console\Exception;
use yii\console\controllers\MigrateController;
use yii\helpers\FileHelper;

class MigrationsController extends MigrateController
{

    /**
     * @var array aliases of directories
     */
    public $migrationLookup = [];

    /**
     * @var array of early found migrations
     */
    private $_cachedLookUp = [];

    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations
     */
    protected function getNewMigrations()
    {
        $migrations = $this->lookupMigrations();

        foreach ($this->getMigrationHistory(null) as $version => $time) {
            if (isset($migrations[$version]))
                unset($migrations[$version]);
        }

        $migrations = array_keys($migrations);
        sort($migrations);

        return $migrations;
    }

    /**
     * Returns found migrations.
     * @return array list of migrations and their path
     */
    protected function lookupMigrations()
    {

        if (!empty($this->_cachedLookUp))
            return $this->_cachedLookUp;

        $this->migrationLookup[] = $this->migrationPath;

        $migrations = [];
        $ds = DIRECTORY_SEPARATOR;

        foreach ($this->migrationLookup as $migrationPath) {

            $basePath = Yii::getAlias($migrationPath);
            if (false !== strpos($basePath, '/migrations')) {
                $patterns[] = $basePath . $ds . '*.php';
            } elseif (false !== strpos($basePath, '/modules')) {
                $patterns[] = $basePath . $ds . '*' . $ds . 'migrations' . $ds . '*.php';
            } else {
                $patterns[] = $basePath . $ds . '*' . $ds . 'migrations' . $ds . '*.php';
                $patterns[] = $basePath . $ds . '*' . $ds . 'modules' . $ds . 'migrations' . $ds . '*.php';
            }

            foreach ($patterns as $pattern) {
                foreach (glob($pattern) as $path) {
                    if (preg_match('/^(m(\d{6}_\d{6})_.*?)\.php$/', basename($path), $matches) /*&& !isset($applied[$matches[2]])*/ && is_file($path)) {
                        $migrations[$matches[1]] = $path;
                    }
                }
            }
        }
        $this->_cachedLookUp = $migrations;

        return $migrations;
    }

    /**
     * Creates a new migration instance.
     * @param string $class the migration class name
     * @return \yii\db\MigrationInterface the migration instance
     */
    protected function createMigration($class)
    {
        $file = $this->lookupMigrations()[$class];
        require_once($file);

        return new $class();
    }

}
