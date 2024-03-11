<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_age_group".
 *
 * @property int $id
 * @property int $partner_id
 * @property string $name
 * @property int $from_age
 * @property int $to_age
 * @property float $coeff
 *
 * @property Partner $partner
 */
class TravelAgeGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_age_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'name', 'from_age', 'to_age', 'coeff'], 'required'],
            [['partner_id', 'from_age', 'to_age'], 'default', 'value' => null],
            [['partner_id', 'from_age', 'to_age'], 'integer'],
            [['coeff'], 'number'],
            [['name'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Name'),
            'from_age' => Yii::t('app', 'From Age'),
            'to_age' => Yii::t('app', 'To Age'),
            'coeff' => Yii::t('app', 'Coeff'),
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
