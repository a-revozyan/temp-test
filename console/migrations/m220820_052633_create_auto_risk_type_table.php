<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%auto_type}}`.
 */
class m220820_052633_create_auto_risk_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auto_risk_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'status' => $this->integer()->defaultValue(\common\models\AutoRiskType::STATUS['active']),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%auto_risk_type}}');
    }
}
