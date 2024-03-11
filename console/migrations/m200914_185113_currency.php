<?php

use yii\db\Migration;

/**
 * Class m200914_185113_currency
 */
class m200914_185113_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%currency}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'code' => $this->string()->notNull(),
            'rate' => $this->float(),
            'rate_date' => $this->date(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], null);

        $this->addColumn('partner', 'travel_currency_id', $this->integer());

        $this->createIndex(
            'idx-partner-travel_currency_id',
            'partner',
            'travel_currency_id'
        );

        $this->addForeignKey(
            'fk-partner-travel_currency_id',
            'partner',
            'travel_currency_id',
            'currency',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200914_185113_currency cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200914_185113_currency cannot be reverted.\n";

        return false;
    }
    */
}
