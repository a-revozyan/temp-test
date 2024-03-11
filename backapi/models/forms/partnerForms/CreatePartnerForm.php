<?php
namespace backapi\models\forms\partnerForms;

use common\models\Partner;
use common\models\User;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\web\BadRequestHttpException;


class CreatePartnerForm extends Model
{
    public $name;
    public $contract_number;
    public $image;
    public $travel_offer_file; //Страховая оферта
    public $phone;
    public $password;
    public $service_amount;
    public $hook_url;
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'image'], 'required'],
            [['name', 'contract_number', 'phone', 'password', 'password_repeat', 'hook_url'], 'string', 'max' => 255],
            //[['phone'], 'unique', 'targetClass' => User::class, 'targetAttribute' => 'phone'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Password and Repeat password don't match" ],
            [['image'], 'file', 'skipOnEmpty' => false, 'maxSize' => 50 * 1024 * 1024],
            [['travel_offer_file'], 'file', 'skipOnEmpty' => true, 'maxSize' => 50 * 1024 * 1024],
            [['service_amount'], 'integer'],
        ];
    }

    public function save()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!empty($this->phone))
            {
                if (User::find()->where(['phone' => $this->phone])->exists())
                    throw new BadRequestHttpException('Phone was already used for fuser');
                if (\backapi\models\User::find()->where(['phone_number' => $this->phone])->exists())
                    throw new BadRequestHttpException('Phone was already used for user');

                $fuser = new User();
                $fuser->phone = $this->phone;
                $fuser->status = User::STATUS_ACTIVE;
                $fuser->role = User::ROLES['partner'];
                if (!empty($this->password) and !empty($this->password_repeat))
                    $fuser->setPassword($this->password);
                $fuser->save();

                $user = new \backapi\models\User();
                $user->username = $this->phone;
                $user->status = \backapi\models\User::STATUS_ACTIVE;
                $user->generateAuthKey();
                $user->email = "default@partner.com";
                $user->created_at = time();
                $user->updated_at = time();
                if (!empty($this->password) and !empty($this->password_repeat))
                    $user->setPassword($this->password);
                $user->save();

                $auth = Yii::$app->authManager;
                if (!($auth_item = $auth->getRole(Partner::PARTNER_ROLE_NAME)))
                {
                    $auth_item = $auth->createRole(Partner::PARTNER_ROLE_NAME);
                    $auth->add($auth_item);
                }
                $auth->assign($auth_item, $user->id);
            }

            $partner = new Partner();
            $partner->name = $this->name;
            if (!empty($this->phone))
            {
                $partner->service_amount = $this->service_amount;
                $partner->hook_url = $this->hook_url;
            }
            $partner->f_user_id = $fuser->id ?? null;
            $partner->contract_number = $this->contract_number;
            $partner->created_at = time();
            $partner->updated_at = time();
            $partner->status = Partner::STATUS['active'];
            $partner->image = 'partner' . $partner->created_at . '.' . $this->image->extension;
            $this->image->saveAs(Yii::getAlias('@frontend') . "/web/uploads/partners/" . $partner->image);

            if (!empty($this->travel_offer_file))
            {
                $directoryPath = Yii::getAlias('@frontend') . "/web/uploads/partners/travel_offer_file/";
                if (!is_dir($directoryPath))
                    mkdir($directoryPath, 0777, true);

                $partner->travel_offer_file = 'partner_' . $partner->name . "_" . $partner->created_at . '.' . $this->travel_offer_file->extension;
                $this->travel_offer_file->saveAs($directoryPath . $partner->travel_offer_file);
            }
            $partner->save();

            if (isset($user))
            {
                $user->partner_id = $partner->id;
                $user->save();
            }

            $transaction->commit();
            return $partner;

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}