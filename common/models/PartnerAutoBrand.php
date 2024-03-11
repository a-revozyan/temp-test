<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner_auto_brand".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $created_at
 * @property bool|null $created_by_saas
 * @property bool|null $created_by_car_price_bot
 */
class PartnerAutoBrand extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_auto_brand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['created_by_saas', 'created_by_car_price_bot'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
