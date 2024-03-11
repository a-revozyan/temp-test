<?php

use yii\db\Migration;

/**
 * Class m201108_112622_covid
 */
class m201108_112622_covid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel_program', 'has_covid', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201108_112622_covid cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201108_112622_covid cannot be reverted.\n";

        return false;
    }
    */
}
