<?php

namespace frontend\models\OsagoStepForms;

use common\custom\exceptions\BadRequestHttpException;
use common\models\BridgeCompany;
use common\models\Osago;
use common\models\Partner;
use Yii;

class PayWithoutQueueForm extends \yii\base\Model
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
        if ($osago->status == Osago::STATUS['received_policy'])
            return $osago;

        if ($osago->partner_id == Partner::PARTNER['kapital'])
            throw new BadRequestHttpException('You can not pay for Kapital insurance company');

        if ($order_id = $osago->create_osago_in_partner_system()) {
            $osago->order_id_in_gross = $order_id;
            $osago->save();

            $response_arr = $osago->partner_payment();
        }

        return $osago;
    }
}
