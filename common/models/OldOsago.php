<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "old_osago".
 *
 * @property int $id
 * @property int|null $old_id
 * @property string|null $created_at
 * @property string|null $insurer_name
 * @property string|null $policy_number
 * @property string|null $insurer_phone_number
 * @property string|null $owner
 * @property int|null $amount_uzs
 * @property int|null $status
 * @property string|null $payment_type
 * @property string|null $imported_at
 */
class OldOsago extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'old_osago';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['old_id', 'amount_uzs', 'status'], 'default', 'value' => null],
            [['old_id', 'amount_uzs', 'status'], 'integer'],
            [['created_at', 'imported_at'], 'safe'],
            [['insurer_name', 'policy_number', 'insurer_phone_number', 'owner', 'payment_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'old_id' => 'Old ID',
            'created_at' => 'Created At',
            'insurer_name' => 'Insurer Name',
            'policy_number' => 'Policy Number',
            'insurer_phone_number' => 'Insurer Phone Number',
            'owner' => 'Owner',
            'amount_uzs' => 'Amount Uzs',
            'status' => 'Status',
            'payment_type' => 'Payment Type',
            'imported_at' => 'Imported At',
        ];
    }
}
