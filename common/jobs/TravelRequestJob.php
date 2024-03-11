<?php
namespace common\jobs;

use common\models\Travel;
use common\services\PaymentService;
use common\services\SMSService;
use common\services\TelegramService;

class TravelRequestJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $travel_id;

    public function execute($queue)
    {
        $travel = Travel::findOne($this->travel_id);
        if (!($travel->send_save_to_partner_system(60, 120)))
        {
            if (!Travel::find()->where(['id' => $travel->id])->andWhere(['in', 'status', [Travel::STATUSES['canceled']]])->exists())
            {
                $travel->status = Travel::STATUSES['canceled'];
                $travel->save();
                PaymentService::cancel(Travel::className(), $travel->id);
            }
            
            SMSService::sendMessage($travel->user->phone, "Sug'urta Bozor. Polis TRAVEL NE OFORMLEN iz-za oshibki postavshika. OPLATA OTMENENA");
            TelegramService::send($travel, true);
        }
    }
}