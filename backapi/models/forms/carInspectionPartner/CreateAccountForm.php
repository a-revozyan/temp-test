<?php
namespace backapi\models\forms\carInspectionPartner;

use common\models\CarInspection;
use common\models\CarInspectionFile;
use common\models\Partner;
use common\models\PartnerAccount;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;

class CreateAccountForm extends Model
{
    public $partner_id;
    public $amount;
    public $note;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id', 'amount'], 'required'],
            [['partner_id'], 'integer'],
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id'],
                'filter' => function($query){
                    return $query->leftJoin('f_user', 'partner.f_user_id = f_user.id')
                        ->andWhere([
                            'f_user.role' => User::ROLES['partner']
                        ]);
                }
            ],
            [['note'], 'string'],
            [['amount'], 'integer', 'min' => 0]
        ];
    }

    public function save()
    {
        $partner_account = new PartnerAccount();
        $partner_account->partner_id = $this->partner_id;
        $partner_account->amount = $this->amount;
        $partner_account->note = $this->note;
        $partner_account->user_id = Yii::$app->user->identity->getId();
        $partner_account->created_at = date('Y-m-d H:i:s');
        $partner_account->save();

        return $partner_account;
    }
}