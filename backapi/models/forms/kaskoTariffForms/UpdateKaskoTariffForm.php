<?php
namespace backapi\models\forms\kaskoTariffForms;

use common\helpers\GeneralHelper;
use common\models\KaskoTariff;
use common\models\Partner;
use yii\base\Model;


class UpdateKaskoTariffForm extends Model
{
    public $tariff_id;
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
            [['partner_id', 'name', 'is_conditional', 'tariff_id'], 'required'],
            [['amount'], 'required', 'when' => function($model){
                return !$model->is_islomic;
            }],
            [['amount'], 'double'],
            [['partner_id', 'tariff_id', 'is_islomic', 'min_year', 'max_year', 'min_price', 'max_price'], 'integer'],
            [['is_conditional'], 'integer', 'min' => 0, 'max' => 1],
            [[
                'name', 'franchise_ru', 'franchise_uz', 'franchise_en', 'only_first_risk_ru',
                'only_first_risk_en', 'only_first_risk_uz'
              ], 'string'],
            [[
                'name', 'franchise_ru', 'franchise_uz', 'franchise_en', 'only_first_risk_ru',
                'only_first_risk_en', 'only_first_risk_uz'
              ], 'filter', 'filter' => 'trim'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
            [['file'], 'file', 'skipOnEmpty' => true, 'maxSize' => 50 * 1024 * 1024],
        ];
    }

    public function save()
    {
        $kasko_tariff = KaskoTariff::findOne($this->tariff_id);
        $old_file = $kasko_tariff->file;
        $attrs = $this->attributes;
        unset($attrs['file']);
        $kasko_tariff->setAttributes($attrs);
        $kasko_tariff->amount_kind = "P";
        $kasko_tariff->save();

        if (!empty($this->file))
        {
            $kasko_tariff = $kasko_tariff->saveFile($kasko_tariff, $this->file, $old_file);
            $kasko_tariff->file = GeneralHelper::env('backend_project_website') . $kasko_tariff->file;
        }

        return $kasko_tariff;
    }

}