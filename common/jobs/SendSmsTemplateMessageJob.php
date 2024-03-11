<?php
namespace common\jobs;

use common\helpers\GeneralHelper;
use common\models\SmsHistory;
use common\models\SmsTemplate;
use common\models\Token;
use common\services\SMSService;

class SendSmsTemplateMessageJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $sms_template;
    public $user;

    public function execute($queue)
    {
        $sms_template = SmsTemplate::findOne($this->sms_template->id);

        if (
            $sms_template->status == SmsTemplate::STATUS['paused']
            or
            SmsHistory::find()
                ->where(['f_user_id' => $this->user->id, 'sms_template_id' => $sms_template->id])
                ->andWhere(['not', ['status' => SmsHistory::STATUS['created']]])->exists()
        )
            return 0;


        SMSService::sendMessageAll(
            $this->user->phone,
            $this->sms_template->text,
            $this->user->telegram_chat_ids(),
            $this->sms_template
        );

        $tried_users_count = SmsHistory::find()->where(['sms_template_id' => $sms_template->id])->select('f_user_id')->distinct()->count();
        if ($tried_users_count == $sms_template->all_users_count)
        {
            $sms_template->status = SmsTemplate::STATUS['ended'];
            $sms_template->end_date = date('Y-m-d H:i:s');
            $sms_template->save();
        }
//        TelegramService::sendMessage(GeneralHelper::env('web_app_telegram_bot_token'), $this->user->telegram_chat_id, $this->sms_template->text, TelegramService::METHOD["sendVideo"], "http://clips.vorwaerts-gmbh.de/VfE_html5.mp4");
    }
}