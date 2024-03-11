<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner_month_car_price_pay".
 *
 * @property int $id
 * @property int|null $partner_id
 * @property string|null $month
 * @property bool|null $is_paid
 * @property string|null $updated_at
 */
class PartnerMonthCarPricePay extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_month_car_price_pay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id'], 'default', 'value' => null],
            [['partner_id'], 'integer'],
            [['is_paid'], 'boolean'],
            [['month', 'updated_at'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Partner ID',
            'month' => 'Month',
            'is_paid' => 'Is Paid',
            'updated_at' => 'Updated At',
        ];
    }
}
