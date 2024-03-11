<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "autobrand".
 *
 * @property int $id
 * @property string $trans_no
 * @property string $card_id
 * @property string $card_mask
 * @property integer $status
 * @property integer $f_user_id
 * @property integer $created_at
 * @property string|null $payment_type
 *
 */
class SavedCard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'saved_card';
    }

    public const STATUS = [
        'created' => 0,
        'saved' => 1,
        'verified' => 2,
    ];

    public const PAYMENT_TYPE = [
        'payze' => 'payze',
        'payme' => 'payme',
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trans_no', 'card_mask'], 'string', 'max' => 255],
            [['card_id'], 'string', 'max' => 3000],
            [['status', 'f_user_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'trans_no' => Yii::t('app', 'trans_no'),
            'card_id' => Yii::t('app', 'card_id'),
            'card_mask' => Yii::t('app', 'card_mask'),
            'status' => Yii::t('app', 'status'),
            'f_user_id' => Yii::t('app', 'f_user_id'),
            'created_at' => Yii::t('app', 'created_at'),
        ];
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'card_mask' => $this->card_mask,
        ];
    }
}
