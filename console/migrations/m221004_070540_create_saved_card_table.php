<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%saved_card}}`.
 */
class m221004_070540_create_saved_card_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%saved_card}}', [
            'id' => $this->primaryKey(),
            'trans_no' => $this->string(),
            'card_id' => $this->string(),
            'card_mask' => $this->string(),
            'status' => $this->integer(),
            'f_user_id' => $this->integer(),
            'created_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%saved_card}}');
    }
}
