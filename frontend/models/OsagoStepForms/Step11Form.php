<?php

namespace frontend\models\OsagoStepForms;

use common\helpers\GeneralHelper;
use common\models\Osago;
use common\models\Partner;
use frontend\models\OsagoapiForms\GetPartnersForm;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class Step11Form extends \yii\base\Model
{
    public $osago_uuid;
    public $partner_id;
    public $owner_with_accident;

    public function rules()
    {
        return [
            [['osago_uuid', 'partner_id'], 'required'],
            [['partner_id'], 'integer'],
            [['partner_id'], 'in', 'range' => [Partner::PARTNER['gross'], Partner::PARTNER['neo'], Partner::PARTNER['insonline'], Partner::PARTNER['kapital']]],
            [['osago_uuid'], 'string', 'max' => 255],
            [['osago_uuid'], UuidValidator::className()],
            [['osago_uuid'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['osago_uuid' => 'uuid'], 'filter' => function($query){
                return $query
                    ->andWhere(['not in', 'status', [
                        Osago::STATUS['step1'], Osago::STATUS['payed'], Osago::STATUS['waiting_for_policy'], Osago::STATUS['received_policy']
                    ]]);
            }],
            [['owner_with_accident'], 'boolean'],
            [['owner_with_accident'], 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'osago_uuid' => Yii::t('app', 'osago_uuid'),
            'partner_id' => Yii::t('app', 'partner_id'),
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function save()
    {
        $osago = Osago::findOne(['uuid' => $this->osago_uuid]);

        if (!in_array($this->partner_id, $this->getAvailablePartnerIds($osago)))
            throw new BadRequestHttpException(Yii::t('app', 'Please, send suitable partners_id'));

        if (!empty($osago->bridge_company_id) and $osago->is_juridic == 1 and $fuser = GeneralHelper::fUser())
            $osago->f_user_id = $fuser->id;

        $osago->partner_id = $this->partner_id;
        $osago->owner_with_accident = $this->owner_with_accident;

        if ($osago->partner_id == Partner::PARTNER['insonline'] or $osago->is_juridic)
            $osago->owner_with_accident = false;

        if ($osago->partner_id == Partner::PARTNER['kapital'] and $osago->number_drivers_id == Osago::TILL_5_NUMBER_DRIVERS_ID and $osago->region_id == Osago::REGION_TASHKENT_ID)
            $osago->owner_with_accident = true;

        $osago->status = Osago::STATUS['step11'];
        if ($osago->is_juridic)
            $osago->status = Osago::STATUS['step3'];

        $osago->save();

        $osago->setAccidentAmount();

        return $osago;
    }

    public function getAvailablePartnerIds($osago)
    {
        $partners_form = new GetPartnersForm();
        $partners_form->autonumber = $osago->autonumber;
        $partners_form->number_drivers_id = $osago->number_drivers_id;
        $partners_form->period_id = $osago->period_id;
        $partners_form->partner_ability = $osago->partner_ability;
        $partners = $partners_form->save();

        return ArrayHelper::map($partners, 'id', 'id');
    }
}