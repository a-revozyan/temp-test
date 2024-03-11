<?php

use yii\db\Migration;

/**
 * Class m220415_073015_risk_category
 */
class m220415_073015_risk_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('kasko_risk_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ], null);

        $this->addColumn('kasko_risk', 'category_id', $this->integer());
        $this->addColumn('kasko_risk', 'amount', $this->float());

        $this->createIndex(
            'idx-kasko_risk-category_id',
            'kasko_risk',
            'category_id'
        );

        $this->addForeignKey(
            'fk-kasko_risk-category_id',
            'kasko_risk',
            'category_id',
            'kasko_risk_category',
            'id',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220415_073015_risk_category cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220415_073015_risk_category cannot be reverted.\n";

        return false;
    }
    */
}
