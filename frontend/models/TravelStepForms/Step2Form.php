<?php

namespace frontend\models\TravelStepForms;

use common\helpers\DateHelper;
use common\models\KapitalSugurtaRequest;
use common\models\Travel;
use common\models\TravelMember;
use common\services\fond\FondService;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;

class Step2Form extends Model
{
    public $members;
    public $travel_uuid;

    public $check_members = false;

    public function rules()
    {
        return  [
            [
                [
                    'travel_uuid',
                    'members'
                ],
                'required'
            ],
            [['travel_uuid'], 'string', 'max' => 255],
            [['travel_uuid'], UuidValidator::className()],
            ['travel_uuid', 'exist', 'skipOnError' => true,
                'targetClass' => Travel::className(),
                'targetAttribute' => ['travel_uuid' => 'uuid'],
                'filter' => function($query){
                    $query->andWhere(['in', 'status', [
                            Travel::STATUSES['step1'],
                            Travel::STATUSES['step2'],
                            Travel::STATUSES['step3'],
                        ]
                        ]);
                }
            ],
            ['members', 'each', 'rule' => ['checkMemberJson']],
        ];
    }

    public function checkMemberJson(){
        if (!$this->check_members)
            return 0;

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

            if (!ctype_alpha(str_replace(' ', '', $member['name'])))
            {
                $this->addError('members',"Each member's name must consist only latin letters");
                break;
            }

            $travel_member = TravelMember::find()->leftJoin('travel', 'travel_id=travel.id')
                ->where([
                    'travel_member.id' => $member['id'],
                    'travel.uuid' => ((array)json_decode(\Yii::$app->request->rawBody))['travel_uuid'],
                ])->one();

            if ($travel_member == null)
            {
                $this->addError('members',"Each member must be of user's travel order");
                break;
            }
        }

        if (count($this->members) != $travel_members_count = count(Travel::findOne(['uuid' => $this->travel_uuid])->travelMembers))
            $this->addError('members', "There must be $travel_members_count members");
    }

    public function attributeLabels()
    {
        return [
            'members' => Yii::t('app', 'members'),
            'travel_uuid' => Yii::t('app', 'travel_uuid'),
        ];
    }

    public function save()
    {
        $this->check_members = true;
        if (!$this->validate(array_keys($this->attributes)))
            return Yii::$app->controller->sendFailedResponse($this->errors, 422);
        
        $travel = Travel::findOne(['uuid' => $this->travel_uuid]);

        if (!isset($this->members))
            $this->members = [];

        foreach ($this->members as $member)
        {
            $travel_member = TravelMember::findOne(['id' => $member->id]);
            $member_info = FondService::getDriverInfoByPinflOrBirthday($member->passport_series, $member->passport_number, null, $travel_member->birthday, true);

            TravelMember::updateAll([
                'name' => $member->name,
                'passport_series' => $member->passport_series,
                'passport_number' => $member->passport_number,
                'first_name' => $member_info['FIRST_NAME_LATIN'],
                'last_name' => $member_info['LAST_NAME_LATIN'],
                'pinfl' => $member_info['PINFL'],
            ], [
                'id' => $member->id
            ]);
        }

        $travel_members = $travel->travelMembers;
        $insurer = $travel_members[0];
        foreach ($travel_members as $travel_member) {
            if ($insurer->age < $travel_member->age)
                $insurer = $travel_member;
        }

        $travel->insurer_name = $insurer->name;
        $travel->insurer_passport_series = $insurer->passport_series;
        $travel->insurer_passport_number = $insurer->passport_number;
        $travel->status = Travel::STATUSES['step2'];
        $travel->f_user_id = Yii::$app->user->identity->getId();
        $travel->save();

        return Travel::findOne($travel->id)->getFullClientArr();
    }

}