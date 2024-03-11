<?php

use yii\db\Migration;

/**
 * Class m201006_171914_alter_travel1
 */
class m201006_171914_alter_travel1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('travel', 'insurer_birthday', $this->date()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201006_171914_alter_travel1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201006_171914_alter_travel1 cannot be reverted.\n";

        return false;
    }
    */
}
