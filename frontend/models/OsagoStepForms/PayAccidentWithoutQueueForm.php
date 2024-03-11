<?php

namespace frontend\models\OsagoStepForms;

use common\custom\exceptions\BadRequestHttpException;
use common\models\Accident;
use common\models\BridgeCompany;
use common\models\Osago;
use common\models\OsagoDriver;
use common\models\Partner;
use Yii;

class PayAccidentWithoutQueueForm extends \yii\base\Model
{
    public $osago_uuid;

    public function rules()
    {
        return [
            [['osago_uuid'], 'required'],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'],
                'filter' => function($query){
                    $bridge_company = BridgeCompany::findOne(['user_id' => Yii::$app->user->id]);
                    return $query
                        ->andWhere(['bridge_company_id' => $bridge_company->id])
                        ->andWhere(['in', 'status', [
                            Osago::STATUS['step3'], Osago::STATUS['received_policy']
                        ]]);
                }
            ],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        $osago_drivers = OsagoDriver::find()->where(['osago_id' => $osago->id, 'with_accident' => true])->all();
        if (count($osago_drivers) == 0 and !$osago->owner_with_accident)
            return $osago;

        if (!$accident = $osago->accident)
            $accident = (new Accident())->save_accident_from_osago($osago, $osago_drivers);

        if ($accident->status == Accident::STATUS['received_policy'])
            return $osago;

        if ($osago->partner_id == Partner::PARTNER['kapital'])
            throw new BadRequestHttpException('You can not pay for Kapital insurance company');

        $accident->get_policy_from_partner($osago);

        return Osago::findOne(['uuid' => $this->osago_uuid]);
    }
}
