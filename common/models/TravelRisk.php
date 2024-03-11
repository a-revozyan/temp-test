<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "travel_risk".
 *
 * @property int $id
 * @property int $partner_id
 * @property string $name_ru
 * @property string $name_uz
 * @property string $name_en
 * @property bool $status
 *
 * @property TravelProgramRisk[] $travelProgramRisks
 * @property Partner $partner
 */
class TravelRisk extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_risk';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'name_ru', 'name_uz', 'name_en', 'status'], 'required'],
            [['amount'], 'double'],
            [['partner_id'], 'default', 'value' => null],
            [['partner_id', 'category_id'], 'integer'],
            [['status'], 'boolean'],
            [['name_ru', 'name_uz', 'name_en'], 'string', 'max' => 255],
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
            'name_ru' => Yii::t('app', 'Name Ru'),
            'name_uz' => Yii::t('app', 'Name Uz'),
            'name_en' => Yii::t('app', 'Name En'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[TravelProgramRisks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelProgramRisks()
    {
        return $this->hasMany(TravelProgramRisk::className(), ['risk_id' => 'id']);
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

    public function getCategory()
    {
        return $this->hasOne(TravelRiskCategory::className(), ['id' => 'category_id']);
    }
}
