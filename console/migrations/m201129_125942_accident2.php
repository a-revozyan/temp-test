<?php

use yii\db\Migration;

/**
 * Class m201129_125942_accident2
 */
class m201129_125942_accident2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('accident', 'begin_date', $this->date()->notNull());
        $this->addColumn('accident', 'end_date', $this->date()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201129_125942_accident2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201129_125942_accident2 cannot be reverted.\n";

        return false;
    }
    */
}
