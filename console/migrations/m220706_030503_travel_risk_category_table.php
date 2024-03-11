<?php

use yii\db\Migration;

/**
 * Class m220706_030503_travel_risk_category_table
 */
class m220706_030503_travel_risk_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%travel_risk_category}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_030503_travel_risk_category_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_030503_travel_risk_category_table cannot be reverted.\n";

        return false;
    }
    */
}
