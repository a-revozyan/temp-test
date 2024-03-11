<?php

use yii\db\Migration;

/**
 * Class m220622_183055_add_step3_date_to_travel
 */
class m220622_183055_add_step3_date_to_travel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%travel}}', 'step3_date', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220622_183055_add_step3_date_to_travel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220622_183055_add_step3_date_to_travel cannot be reverted.\n";

        return false;
    }
    */
}
