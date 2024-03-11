<?php

use yii\db\Migration;

/**
 * Class m220820_052733_add_auto_type_id_to_automodel
 */
class m220820_052733_add_auto_risk_type_id_to_automodel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('automodel', 'auto_risk_type_id', $this->integer());

        $this->addForeignKey(
            'fk-automodel-auto_risk_type_id',
            'automodel',
            'auto_risk_type_id',
            'auto_risk_type',
            'id',
            'set null'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220820_052733_add_auto_risk_type_id_to_automodel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220820_052733_add_auto_type_id_to_automodel cannot be reverted.\n";

        return false;
    }
    */
}
