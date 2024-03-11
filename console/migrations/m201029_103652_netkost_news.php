<?php

use yii\db\Migration;

/**
 * Class m201029_103652_netkost_news
 */
class m201029_103652_netkost_news extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->addColumn('osago', 'viewed', $this->boolean()->defaultValue(false));
    	$this->addColumn('travel', 'viewed', $this->boolean()->defaultValue(false));
    	$this->addColumn('kasko', 'viewed', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201029_103652_netkost_news cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201029_103652_netkost_news cannot be reverted.\n";

        return false;
    }
    */
}
