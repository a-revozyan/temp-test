<?php

namespace frontend\models\TravelStepForms;

use common\helpers\DateHelper;
use common\models\Country;
use common\models\Travel;
use common\models\TravelCountry;
use common\models\TravelExtraInsurance;
use common\models\TravelExtraInsuranceBind;
use common\models\TravelMember;
use common\models\TravelMultiplePeriod;
use common\models\TravelProgram;
use common\models\TravelPurpose;
use common\services\TravelService;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class Step2Form_old extends Model
{
    public $insurer_name;
    public $insurer_passport_series;
    public $insurer_passport_number;
    public $insurer_birthday;
    public $insurer_phone;
    public $insurer_address;
    public $members;
    public $travel_id;

    public function rules()
    {
        return [
            [
                [
                    'insurer_name',
                    'insurer_passport_series',
                    'insurer_passport_number',
                    'insurer_birthday',
                    'insurer_address',
                    'insurer_phone',
                    'travel_id'
                ],
                'required'
            ],
            [['insurer_name', 'insurer_passport_series', 'insurer_passport_number', 'insurer_address'], 'string', 'max' => 255],
            [['insurer_birthday'], 'date', 'format' => 'php: d.m.Y'],
            ['members', 'each', 'rule' => ['checkMemberJson']],
            [['travel_id'], 'integer'],
            ['travel_id', 'exist', 'skipOnError' => true,
                'targetClass' => Travel::className(),
                'targetAttribute' => ['travel_id' => 'id'],
                'filter' => ['status' => 1, 'f_user_id' => Yii::$app->user->id]
            ],
        ];
    }

    public function checkMemberJson(){
        if(!is_array($this->members)){
            $this->addError('members','members is not array!');
        }
        foreach ($this->members as $member) {
            $member = (array)$member;
            if (
                !array_key_exists('id', $member)
                or !array_key_exists('name', $member)
                or !array_key_exists('passport_series', $member)
                or !array_key_exists('passport_number', $member)
            )
            {
                $this->addError('members','Each member must consist of id, name, passport_series, passport_number');
                break;
            }

            $travel_member = TravelMember::find()->leftJoin('travel', 'travel_id=travel.id')
                ->where(['travel_member.id' => $member['id'], 'travel.f_user_id' => Yii::$app->user->id])->one();

            if ($travel_member == null)
            {
                $this->addError('members',"Each member must be of user's travel order");
                break;
            }
        }

        if (count($this->members) != $travel_members_count = count(Travel::findOne($this->travel_id)->travelMembers))
            $this->addError('members', "There must be $travel_members_count members");
    }

    public function attributeLabels()
    {
        return [
            'insurer_name' => Yii::t('app', 'insurer_name'),
            'insurer_phone' => Yii::t('app', 'insurer_phone'),
            'insurer_passport_series' => Yii::t('app', 'insurer_passport_series'),
            'insurer_passport_number' => Yii::t('app', 'insurer_passport_number'),
            'insurer_birthday' => Yii::t('app', 'insurer_birthday'),
            'insurer_address' => Yii::t('app', 'insurer_address'),
            'members' => Yii::t('app', 'members'),
            'travel_id' => Yii::t('app', 'travel_id'),
        ];
    }

    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $travel = Travel::findOne($this->travel_id);

            if (!isset($this->members))
                $this->members = [];

            foreach ($this->members as $member)
                TravelMember::updateAll([
                    'name' => $member->name,
                    'passport_series' => $member->passport_series,
                    'passport_number' => $member->passport_number,
                ], [
                    'id' => $member->id
                ]);

            $travel->insurer_name = $this->insurer_name;
            $travel->insurer_phone = $this->insurer_phone;
            $travel->insurer_passport_series = $this->insurer_passport_series;
            $travel->insurer_passport_number = $this->insurer_passport_number;
            $travel->insurer_birthday = DateHelper::date_format($this->insurer_birthday, 'd.m.Y', 'm.d.Y');
            $travel->insurer_address = $this->insurer_address;
            $travel->status = Travel::STATUSES['step2'];

            $travel->save();

            $transaction->commit();
            return $travel;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}