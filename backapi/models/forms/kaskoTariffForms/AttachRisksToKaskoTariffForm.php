<?php
namespace backapi\models\forms\kaskoTariffForms;

use common\models\Autocomp;
use common\models\KaskoRisk;
use common\models\KaskoRiskCategory;
use common\models\KaskoTariff;
use common\models\Partner;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;


class AttachRisksToKaskoTariffForm extends Model
{
    public $tariff_id;
    public $risk_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariff_id'], 'required'],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => KaskoTariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
            [['risk_ids'], 'each',  'rule' => ['exist', 'skipOnError' => true, 'targetClass' => KaskoRisk::className(), 'targetAttribute' => ['risk_ids' => 'id']]]
        ];
    }

    public function save()
    {
        $kasko_tariff = KaskoTariff::findOne($this->tariff_id);
        $kasko_tariff->unlinkAll('kaskoRisksRelation', true);
        foreach ($this->risk_ids as $risk_id) {
            $risk = KaskoRisk::findOne($risk_id);
            $kasko_tariff->link('kaskoRisksRelation', $risk);
        }

        return $kasko_tariff;
    }

}