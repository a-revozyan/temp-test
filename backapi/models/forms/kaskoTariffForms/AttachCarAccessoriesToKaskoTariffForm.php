<?php
namespace backapi\models\forms\kaskoTariffForms;

use common\models\Autocomp;
use common\models\CarAccessory;
use common\models\KaskoRisk;
use common\models\KaskoRiskCategory;
use common\models\KaskoTariff;
use common\models\Partner;
use common\models\TariffCarAccessoryCoeff;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;


class AttachCarAccessoriesToKaskoTariffForm extends Model
{
    public $tariff_id;
    public $car_accessory_ids;
    public $coeffs;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id'], 'required'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
            [['car_accessory_ids'], 'each',  'rule' => ['exist', 'skipOnError' => true, 'targetClass' => CarAccessory::className(), 'targetAttribute' => ['car_accessory_ids' => 'id']]],
            [['coeffs'], 'each',  'rule' => ['double']]
        ];
    }

    public function save()
    {
        $kasko_tariff = KaskoTariff::findOne($this->tariff_id);
        \Yii::$app->db->createCommand()->delete('tariff_car_accessory_coeff', ['tariff_id' => $kasko_tariff->id])->execute();
        foreach ($this->car_accessory_ids as $key => $car_accessory_id) {
            $tcac = new TariffCarAccessoryCoeff();
            $tcac->setAttributes([
                'tariff_id' => $kasko_tariff->id,
                'car_accessory_id' => $car_accessory_id,
                'coeff' => $this->coeffs[$key],
            ]);
            $tcac->save();
        }

        return $kasko_tariff;
    }

}