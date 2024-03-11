<?php

use yii\db\Migration;

/**
 * Class m220803_093501_add_autobrand_name_automodel_name_autocomp_name_to_kasko
 */
class m220803_093501_add_autobrand_name_automodel_name_autocomp_name_to_kasko extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'autobrand_name', $this->string());
        $this->addColumn('kasko', 'automodel_name', $this->string());
        $this->addColumn('kasko', 'autocomp_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220803_093501_add_autobrand_name_automodel_name_autocomp_name_to_kasko cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220803_093501_add_autobrand_name_automodel_name_autocomp_name_to_kasko cannot be reverted.\n";

        return false;
    }
    */
}
