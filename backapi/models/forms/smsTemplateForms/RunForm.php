<?php
namespace backapi\models\forms\smsTemplateForms;

use common\jobs\SendSmsTemplateMessageJob;
use common\models\SmsHistory;
use common\models\SmsTemplate;
use common\models\User;
use common\services\SMSService;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;


class RunForm extends Model
{
    public $sms_template_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sms_template_id'], 'required'],
            [['sms_template_id'], 'integer'],
            [['sms_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplate::className(), 'targetAttribute' => ['sms_template_id' => 'id']],
        ];
    }

    public function save()
    {
        $sms_template = SmsTemplate::findOne($this->sms_template_id);

        $user_query = User::getSmsFilteredUsersQuery($sms_template->attributes);

        $sms_template->status = SmsTemplate::STATUS['started'];
        $sms_template->all_users_count = $user_query->count();
        $sms_template->save();

//        return $sms_template;

        $user_ids = $user_query->select(['products.f_user_id'])->column();

        $users = User::find()
            ->leftJoin([
                'sms_history' => SmsHistory::find()
                ->where(['sms_template_id' => $sms_template->id])
//                ->andWhere(['not', ['status' => SmsHistory::STATUS['created']]])
            ], 'sms_history.f_user_id = f_user.id')
            ->where([
                'and',
                ['in', 'f_user.id', $user_ids],
                ['sms_history.id' => null]
            ])
            ->all();

        /** @var User $user */
        foreach ($users as $user)
        {
            Yii::$app->queue2->push(new SendSmsTemplateMessageJob([
                'sms_template' => $sms_template,
                'user' => $user,
            ]));
        }

        return $sms_template;
    }

}