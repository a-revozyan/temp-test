<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_auto}}`.
 */
class m231005_130620_create_client_auto_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client_auto}}', [
            'id' => $this->primaryKey(),
            'f_user_id' => $this->integer()->unsigned(),
            'autocomp_id' => $this->integer()->unsigned(),
            'manufacture_year' => $this->smallInteger()->unsigned(),
            'autonumber' => $this->string(),
            'tex_pass_series' => $this->string(),
            'tex_pass_number' => $this->string(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%client_auto}}');
    }
}
