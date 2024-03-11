<?php

use yii\db\Migration;

/**
 * Class m220802_125315_kasko_old_kasko_risk
 */
class m220802_125315_kasko_old_kasko_risk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('kasko_old_kasko_risk', [
            'kasko_id' => $this->integer(),
            'old_kasko_risk_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220802_125315_kasko_old_kasko_risk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220802_125315_kasko_old_kasko_risk cannot be reverted.\n";

        return false;
    }
    */
}
