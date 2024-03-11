<?php

namespace console\controllers\seeder;

use yii\base\Controller;
use yii\helpers\Console;


class BaseSeederController extends Controller
{
    public function insertData($table, $rows, $columns)
    {
        $db = \Yii::$app->db;

        // Begin a transaction
        $transaction = $db->beginTransaction();

        $tableNameForOutput = str_replace('_', ' ', strtoupper($table));

        try {
            // Create a command object
            $command = $db->createCommand();

            // Insert multiple rows using batchInsert()
            $command->batchInsert($table, $rows, $columns)->execute();

            // Commit the transaction
            $transaction->commit();

            Console::output(Console::ansiFormat("$tableNameForOutput are inserted successfully", [Console::FG_GREEN]));

        } catch (\Exception $e) {
            // Roll back the transaction in case of an error
            $transaction->rollBack();
            Console::output($e->getMessage());
            Console::output(Console::ansiFormat("Error while $tableNameForOutput inserting", [Console::FG_RED]));

        }
    }
}
