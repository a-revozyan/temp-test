<?php

namespace console\controllers\seeder;

use yii\helpers\Console;

class DatabaseSeederController extends BaseSeederController
{
    private function truncateTables()
    {
        $db = \Yii::$app->db;

        // All tables which should be truncated
        $tables = [
            "auth_assignment",
            "auth_item_child",
            "auth_item",
            "bridge_company",
            "country",
            "number_drivers",
            "partner",
            "period",
            "product",
            "region",
            "relationship",
            "travel_group_type",
            "user",
        ];

        // Truncate each table using raw SQL query
        foreach ($tables as $table) {
            try {
                $tableName = $db->quoteTableName($table);
                $db->createCommand("TRUNCATE TABLE $tableName RESTART IDENTITY CASCADE")->execute();
            } catch (\Exception $e) {
                Console::output($e->getMessage());
                $tableName = strtoupper($table);
                Console::output(Console::ansiFormat("Error while dropping the table $tableName", [Console::FG_RED]));
            }
        }
    }

    private function runSeeder($truncateTables = false)
    {
        $seeders = [
            "seeder/product-seeder/run",
            "seeder/number-drivers-seeder/run",
            "seeder/period-seeder/run",
            "seeder/region-seeder/run",
            "seeder/partner-seeder/run",
            "seeder/user-seeder/run",
            "seeder/auth-item-seeder/run",
            "seeder/auth-item-child-seeder/run",
            "seeder/auth-assignment-seeder/run",
            "seeder/bridge-company-seeder/run",
            "seeder/relationship-seeder/run",
            "seeder/travel-group-type-seeder/run",
            "seeder/country-seeder/run",
        ];

        if ($truncateTables) {
            $this->truncateTables();
        }

        foreach ($seeders as $command) {
            $this->run($command);
        }
    }

    public function actionRun()
    {
        $this->runSeeder();
    }

    public function actionTruncateRun()
    {
        $this->runSeeder(true);
    }
}