<?php

namespace common\models;

use common\helpers\GeneralHelper;
use dosamigos\ckeditor\CKEditor;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "kasko_tariff".
 *
 * @property int $id
 * @property int $partner_id
 * @property string $name
 * @property string $amount_kind
 * @property float $amount
 *
 * @property Kasko[] $kaskos
 * @property Partner $partner
 * @property KaskoTariffRisk[] $kaskoTariffRisks
 * @property string $file
 * @property string $is_conditional
 * @property string $is_islomic
 * @property integer $min_price
 * @property integer $max_price
 * @property integer $min_year
 * @property integer $max_year
 * @property string|null $franchise_ru
 * @property string|null $franchise_uz
 * @property string|null $franchise_en
 */
class KaskoTariff extends \yii\db\ActiveRecord
{
    public $auto_risk_type_ids;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kasko_tariff';
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'partner';
        $fields[] = 'tariffIslomicAmounts';
        $fields[] = 'tariffCarAccessoryCoeff';
        $fields[] = 'kaskoRisks';
        $fields[] = 'autoRiskTypes';
        $fields['file'] = function ($model){
            return GeneralHelper::env('front_website_send_request_url') . $model->file;
        };

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'name', 'amount_kind', 'amount'], 'required'],
            [['partner_id', "file"], 'default', 'value' => null],
            [["file"], 'file', 'skipOnEmpty' => true,  'maxSize'=>5*1024*1024],
            [['partner_id', 'min_price', 'max_price', 'min_year', 'max_year'], 'integer'],
            [['amount'], 'number'],
            [
                [
                    'name', 'amount_kind',
                ], 'string', 'max' => 255
            ],
            [
                [
                    'franchise_ru', 'franchise_en', 'franchise_uz',
                    'only_first_risk_ru', 'only_first_risk_en', 'only_first_risk_uz'
                ], 'string', 'max' => 10485760
            ],
            ['is_conditional', 'safe'],
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
            'amount_kind' => Yii::t('app', 'Amount Kind'),
            'amount' => Yii::t('app', 'Amount'),
        ];
    }

    /**
     * Gets query for [[Kaskos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskos()
    {
        return $this->hasMany(Kasko::className(), ['tariff_id' => 'id']);
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
     * Gets query for [[KaskoTariffRisks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskoTariffRisks()
    {
        return $this->hasMany(KaskoTariffRisk::className(), ['tariff_id' => 'id']);
    }

    public function getKaskoRisks()
    {
        $lang = GeneralHelper::lang_of_local();
        return $this->hasMany(KaskoRisk::className(), ['id' => 'risk_id'])->select([
            'id',
            "name" => "name_$lang",
            "amount",
            "category_id",
            "description" => "description_$lang",
        ])->via("kaskoTariffRisks")->orderBy(["id" => "asc"])->asArray();
    }

    public function getKaskoRisksRelation()
    {
        return $this->hasMany(KaskoRisk::className(), ['id' => 'risk_id'])->via("kaskoTariffRisks");
    }

    public function saveFile($model, $file, $old_file = '')
    {
        $folder_path = '/uploads/kasko-tariff/' . "$model->id-$model->name" . '/';
        $root = str_replace('\\', '/', Yii::getAlias('@backend') . '/web/');

        if (\yii\helpers\FileHelper::createDirectory($root . $folder_path, $mode = 0775, $recursive = true)) {
            $file_path = $folder_path . $file->baseName . "-" . Yii::$app->security->generateRandomString(5) . "." . $file->extension;
            if ($file->saveAs($root .$file_path))
            {
                $model->file = $file_path;
                if (!empty($old_file) and is_file($root .  $old_file))
                    unlink($root .  $old_file);
            }
        }
        $model->save();

        return $model;
    }

    public function getTariffIslomicAmounts()
    {
        return $this->hasMany(TariffIslomicAmount::className(), ['kasko_tariff_id' => 'id']);
    }

    public function getTariffCarAccessoryCoeff()
    {
        return $this->hasMany(TariffCarAccessoryCoeff::className(), ['tariff_id' => 'id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getCarAccessories()
    {
        return $this->hasMany(CarAccessory::className(), ['id' => 'car_accessory_id'])
            ->via('tariffCarAccessory');
    }

    public function getAutoRiskTypes()
    {
        return $this->hasMany(AutoRiskType::className(), ["id" => "auto_risk_type_id"])->viaTable('auto_risk_kasko_tariff', ['kasko_tariff_id' => "id"]);
    }

    public function getShortArr()
    {
        $lang = GeneralHelper::lang_of_local();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'file' => $this->file,
            'franchise' => $this->{'franchise_' . $lang},
            'is_conditional' => $this->is_conditional,
            'is_islomic' => $this->is_islomic,
            'only_first_risk' => $this->{'only_first_risk_' . $lang},
            'kaskoRisks' => KaskoRisk::getShortArrCollection($this->kaskoRisksRelation),
            'partner' =>  !is_null($this->partner) ? $this->partner->getShortArr() : null,
        ];
    }

    public function getFullArr()
    {
        $arr = [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'file' => !is_null($this->file) ? GeneralHelper::env('backend_project_website') . $this->file : null,
            'franchise_ru' => $this->franchise_ru,
            'franchise_uz' => $this->franchise_uz,
            'franchise_en' => $this->franchise_en,
            'only_first_risk_en' => $this->only_first_risk_en,
            'only_first_risk_uz' => $this->only_first_risk_uz,
            'only_first_risk_ru' => $this->only_first_risk_ru,
            'is_conditional' => $this->is_conditional,
            'is_islomic' => $this->is_islomic,
            'kasko_risks' => KaskoRisk::getForIdNameCollection($this->kaskoRisksRelation),
            'partner' =>  !is_null($this->partner) ? $this->partner->getForIdNameArr() : null,
            'auto_risk_types' => AutoRiskType::getShortArrCollection($this->autoRiskTypes),
            'car_accessories' => TariffCarAccessoryCoeff::getMergeCarAccessoryArrCollection($this->tariffCarAccessoryCoeff),
            'min_year' => $this->min_year,
            'max_year' => $this->max_year,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
        ];

        if ($this->is_islomic)
            $arr = array_merge(
                $arr,
                ['auto_risk_types' => TariffIslomicAmount::getMergeRiskTypeArrCollection($this->tariffIslomicAmounts)]
            );

        return $arr;
    }

    public function getWithPartnerNameArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'partner' =>  !is_null($this->partner) ? $this->partner->getShortArr() : null,
        ];
    }
}
