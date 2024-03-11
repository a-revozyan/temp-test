<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "partner_auto_model".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $partner_auto_brand_id
 * @property string|null $created_at
 * @property bool|null $created_by_saas
 * @property bool|null $created_by_car_price_bot
 * @property PartnerAutoBrand|null $autoBrand
 */
class PartnerAutoModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_auto_model';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_auto_brand_id'], 'default', 'value' => null],
            [['partner_auto_brand_id'], 'integer'],
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
            'partner_auto_brand_id' => 'Partner Auto Brand ID',
            'created_at' => 'Created At',
        ];
    }

    public function getAutoBrand()
    {
        return $this->hasOne(PartnerAutoBrand::class, ['id' => 'partner_auto_brand_id']);
    }

    public function getWithBrand()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'auto_brand' => $this->autoBrand->getShortArr(),
        ];
    }
}
