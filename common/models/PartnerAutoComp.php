<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner_auto_comp".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $partner_auto_model_id
 * @property bool|null $created_by_saas
 * @property bool|null $created_by_car_price_bot
 * @property string|null $created_at
 */
class PartnerAutoComp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_auto_comp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_auto_model_id'], 'default', 'value' => null],
            [['partner_auto_model_id'], 'integer'],
            [['created_by_saas', 'created_by_car_price_bot'], 'boolean'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function getAutoModel()
    {
        return $this->hasOne(PartnerAutoModel::class, ['id' => 'partner_auto_model_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'partner_auto_model_id' => 'Partner Auto Model ID',
            'created_by_saas' => 'Created By Saas',
            'created_by_car_price_bot' => 'Created By Car Price Bot',
            'created_at' => 'Created At',
        ];
    }
}
