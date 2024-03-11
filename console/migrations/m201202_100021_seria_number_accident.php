<?php

use yii\db\Migration;

/**
 * Class m201202_100021_seria_number_accident
 */
class m201202_100021_seria_number_accident extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('accident', 'insurer_passport_series', $this->string());
        $this->addColumn('accident', 'insurer_passport_number', $this->string());
        $this->addColumn('accident_insurer', 'passport_series', $this->string());
        $this->addColumn('accident_insurer', 'passport_number', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201202_100021_seria_number_accident cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201202_100021_seria_number_accident cannot be reverted.\n";

        return false;
    }
    */
}
