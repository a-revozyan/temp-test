<?php
namespace backapi\models\forms\kaskoTariffForms;

use common\helpers\GeneralHelper;
use common\models\Autocomp;
use common\models\AutoRiskType;
use common\models\KaskoRisk;
use common\models\KaskoRiskCategory;
use common\models\KaskoTariff;
use common\models\Partner;
use common\models\TariffIslomicAmount;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;


class CreateKaskoTariffForm extends Model
{
    public $amount;
    public $name;
    public $franchise_ru;
    public $franchise_uz;
    public $franchise_en;
    public $only_first_risk_ru;
    public $only_first_risk_uz;
    public $only_first_risk_en;
    public $partner_id;
    public $is_conditional;
    public $file;
    public $is_islomic;

    public $min_price;
    public $max_price;
    public $min_year;
    public $max_year;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'name', 'is_conditional'], 'required'],
            [['amount'], 'required', 'when' => function($model){
                return !$model->is_islomic;
            }],
            [['amount'], 'double'],
            [['partner_id', 'is_islomic', 'is_conditional', 'min_year', 'max_year', 'min_price', 'max_price'], 'integer'],
            [['is_conditional', 'is_islomic'], 'in', 'range' => [0,1]],
            [[
                'name', 'franchise_ru', 'franchise_uz', 'franchise_en', 'only_first_risk_ru',
                'only_first_risk_en', 'only_first_risk_uz'
              ], 'string'],
            [[
                'name', 'franchise_ru', 'franchise_uz', 'franchise_en', 'only_first_risk_ru',
                'only_first_risk_en', 'only_first_risk_uz'
              ], 'filter', 'filter' => 'trim'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['file'], 'file', 'skipOnEmpty' => true, 'maxSize' => 50 * 1024 * 1024],
        ];
    }

    public function save()
    {
        $kasko_tariff = new KaskoTariff();
        $kasko_tariff->setAttributes($this->attributes);
        $kasko_tariff->amount_kind = "P";
        $kasko_tariff->save();

        if (!empty($this->file))
        {
            $kasko_tariff = $kasko_tariff->saveFile($kasko_tariff, $this->file);
            $kasko_tariff->file = GeneralHelper::env('backend_project_website') . $kasko_tariff->file;
        }

        return KaskoTariff::find()->with('tariffIslomicAmounts.autoRiskType')->where(['id' => $kasko_tariff->id])->one();
    }

}