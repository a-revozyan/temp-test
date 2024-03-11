<?php

use yii\db\Migration;

/**
 * Class m201013_110303_kasko3
 */
class m201013_110303_kasko3 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('kasko', 'trans_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201013_110303_kasko3 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201013_110303_kasko3 cannot be reverted.\n";

        return false;
    }
    */
}
