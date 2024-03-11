<?php

use yii\db\Migration;

/**
 * Class m200829_171510_osago_ref_amounts
 */
class m200829_171510_osago_ref_amounts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {        
        $this->createTable('{{%osago_amount}}', [
            'id' => $this->primaryKey(),
            'insurance_premium' => $this->float()->notNull(),
            'insurance_amount' => $this->float()->notNull(),
        ], null);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200829_171510_osago_ref_amounts cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200829_171510_osago_ref_amounts cannot be reverted.\n";

        return false;
    }
    */
}
