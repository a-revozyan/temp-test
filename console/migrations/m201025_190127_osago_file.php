<?php

use yii\db\Migration;

/**
 * Class m201025_190127_osago_file
 */
class m201025_190127_osago_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('osago', 'tech_passport_file', 'tech_passport_file_front');
        $this->addColumn('osago', 'tech_passport_file_back', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201025_190127_osago_file cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201025_190127_osago_file cannot be reverted.\n";

        return false;
    }
    */
}
