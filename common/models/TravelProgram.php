<?php

namespace common\models;

use common\helpers\GeneralHelper;
use Yii;

/**
 * This is the model class for table "travel_program".
 *
 * @property int $id
 * @property string $name
 * @property string $price
 * @property int $partner_id
 * @property bool $status
 * @property bool $has_covid
 *
 * @property Travel[] $travels
 * @property Partner $partner
 * @property TravelProgramCountry[] $travelProgramCountries
 * @property TravelProgramPeriod[] $travelProgramPeriods
 * @property TravelProgramRisk[] $travelProgramRisks
 */
class TravelProgram extends \yii\db\ActiveRecord
{
    public $countries;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'travel_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'partner_id', 'status', 'price'], 'required'],
            [['partner_id'], 'default', 'value' => null],
            [['partner_id', 'price'], 'integer'],
            [['status', 'has_covid'], 'boolean'],
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
            'name' => Yii::t('app', 'Name'),
            'partner_id' => Yii::t('app', 'Partner ID'),
            'status' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Price'),
        ];
    }

    /**
     * Gets query for [[Travels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravels()
    {
        return $this->hasMany(Travel::className(), ['program_id' => 'id']);
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

    /**
     * Gets query for [[TravelProgramCountries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelProgramCountries()
    {
        return $this->hasMany(TravelProgramCountry::className(), ['program_id' => 'id']);
    }

    /**
     * Gets query for [[TravelProgramPeriods]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelProgramPeriods()
    {
        return $this->hasMany(TravelProgramPeriod::className(), ['program_id' => 'id']);
    }

    /**
     * Gets query for [[TravelProgramRisks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTravelProgramRisks()
    {
        return $this->hasMany(TravelProgramRisk::className(), ['program_id' => 'id']);
    }

    public function getRisks()
    {
        $lang = GeneralHelper::lang_of_local();
        return $this->hasMany(TravelRisk::className(), ['id' => 'risk_id'])
            ->select([
                'id' => 'id',
                'name' => "name_$lang",
                "status" => "status",
                'category_id',
                'amount',
            ])
            ->via("travelProgramRisks")->asArray();
    }
}