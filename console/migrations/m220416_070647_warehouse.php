<?php

use yii\db\Migration;

/**
 * Class m220416_070647_warehouse
 */
class m220416_070647_warehouse extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('warehouse', [
            'id' => $this->primaryKey(),
            'partner_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'series' => $this->string()->notNull(),
            'number' => $this->string()->notNull(),
        ]);

        $this->createIndex(
            'idx-warehouse-partner_id',
            'warehouse',
            'partner_id'
        );

        $this->addForeignKey(
            'fk-warehouse-partner_id',
            'warehouse',
            'partner_id',
            'partner',
            'id',
            'RESTRICT'
        );

        $this->createIndex(
            'idx-warehouse-product_id',
            'warehouse',
            'product_id'
        );

        $this->addForeignKey(
            'fk-warehouse-product_id',
            'warehouse',
            'product_id',
            'product',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220416_070647_warehouse cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220416_070647_warehouse cannot be reverted.\n";

        return false;
    }
    */
}
