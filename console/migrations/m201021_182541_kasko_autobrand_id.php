<?php

use yii\db\Migration;

/**
 * Class m201021_182541_kasko_autobrand_id
 */
class m201021_182541_kasko_autobrand_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('kasko', 'autocomp_id', $this->integer());
        $this->alterColumn('kasko', 'year', $this->integer());
        $this->addColumn('kasko', 'autobrand_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201021_182541_kasko_autobrand_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201021_182541_kasko_autobrand_id cannot be reverted.\n";

        return false;
    }
    */
}
