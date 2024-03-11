<?php

namespace frontend\models\OsagoStepForms;

use common\models\Osago;
use common\models\Partner;
use common\services\TelegramService;
use thamtech\uuid\helpers\UuidHelper;
use thamtech\uuid\validators\UuidValidator;
use Yii;

class CheckOsagoFromKapitalForm extends \yii\base\Model
{
    public $osago_uuid;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['osago_uuid'], UuidValidator::className()],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        if ($osago->partner_id == Partner::PARTNER['kapital'] and $osago->status == Osago::STATUS['step4'])
            $osago->partner_payment();

       return true;
    }
}