<?php
namespace backapi\models\forms\fUserForms;

use common\helpers\GeneralHelper;
use common\jobs\SendMessageJob;
use common\models\Promo;
use common\models\User;
use Yii;
use yii\base\Model;

class SendUniqueuLinkForm extends Model
{
    public $user_id;
    public $phone;
    public $amount;
    public $amount_type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'amount_type'], 'required'],
            [['user_id'], 'required', 'when' => function($model) {
                return empty($this->phone);
            }],
            [['phone'], 'required', 'when' => function($model) {
                return empty($this->user_id);
            }],
            [['user_id', 'amount', 'amount_type'], 'integer'],
            [['amount_type'], 'in', 'range' => Promo::AMOUNT_TYPE],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function save()
    {
        $fuser = null;
        if (!empty($this->user_id))
            $fuser = User::findOne($this->user_id);

        if (!is_null($fuser))
            $this->phone = $fuser->phone;

        $promo = new Promo();
        $promo->code = GeneralHelper::generateRandomString([Promo::className(), 'code'], 10);
        $promo->amount_type = $this->amount_type;
        $promo->amount = -1 * abs($this->amount);
        $promo->number = 1;
        $promo->type = Promo::TYPE['unique_link'];
        $promo->status = Promo::STATUS['active'];
        $promo->save();

        Yii::$app->queue1->push(new SendMessageJob([
            'phone' => $this->phone,
            'message' => Yii::t("app", "Sizga Sug'urtabozordan chegirmali havola yuborildi: ") .  GeneralHelper::env('front_website_url') . "/osago?unique_promo=$promo->code"
        ]));

        return true;
    }

}