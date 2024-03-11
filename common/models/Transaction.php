<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $partner_id
 * @property string $trans_no
 * @property float $amount
 * @property string $trans_date
 * @property float|null $perform_time
 * @property float|null $cancel_time
 * @property float $create_time
 * @property int|null $reason
 * @property string $payment_type
 * @property string|null $token
 * @property int $status
 *
 * @property Partner $partner
 */
class Transaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'trans_no', 'amount', 'trans_date', 'create_time', 'payment_type', 'status'], 'required'],
            [['partner_id', 'reason', 'status'], 'default', 'value' => null],
            [['partner_id', 'reason', 'status'], 'integer'],
            [['amount', 'perform_time', 'cancel_time', 'create_time'], 'number'],
            [['trans_date'], 'safe'],
            [['trans_no', 'payment_type', 'token'], 'string', 'max' => 255],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'trans_no' => Yii::t('app', 'Trans No'),
            'amount' => Yii::t('app', 'Amount'),
            'trans_date' => Yii::t('app', 'Trans Date'),
            'perform_time' => Yii::t('app', 'Perform Time'),
            'cancel_time' => Yii::t('app', 'Cancel Time'),
            'create_time' => Yii::t('app', 'Create Time'),
            'reason' => Yii::t('app', 'Reason'),
            'payment_type' => Yii::t('app', 'Payment Type'),
            'token' => Yii::t('app', 'Token'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Partner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartner()
    {
        return $this->hasOne(Partner::className(), ['id' => 'partner_id']);
    }
}
