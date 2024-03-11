<?php

use yii\db\Migration;

/**
 * Class m201110_051750_pdf_number
 */
class m201110_051750_pdf_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kasko', 'policy_order', $this->integer());
        $this->addColumn('kasko', 'policy_number', $this->string());

        $this->addColumn('travel', 'policy_order', $this->integer());
        $this->addColumn('travel', 'policy_number', $this->string());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201110_051750_pdf_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201110_051750_pdf_number cannot be reverted.\n";

        return false;
    }
    */
}
