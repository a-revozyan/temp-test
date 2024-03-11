<?php
namespace backapi\models\forms\partnerForms;

use common\models\User;
use common\models\Partner;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;


class UpdatePartnerForm extends Model
{
    public $name;
    public $contract_number;
    public $image;
    public $travel_offer_file; //Страховая оферта
    public $status;
    public $id;
    public $phone;
    public $password;
    public $password_repeat;
    public $service_amount;
    public $hook_url;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'status', 'id'], 'required'],
            [['name', 'contract_number', 'phone', 'password', 'password_repeat', 'hook_url'], 'string', 'max' => 255],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Password and Repeat password don't match" ],
            [['status', 'id', 'service_amount'], 'integer'],
            [['image'], 'file', 'skipOnEmpty' => true, 'maxSize' => 50 * 1024 * 1024],
            [['travel_offer_file'], 'file', 'skipOnEmpty' => true, 'maxSize' => 50 * 1024 * 1024],
            ['id', 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $partner = Partner::findOne($this->id);
            if (!empty($this->phone))
            {
                if (User::find()
                    ->andFilterWhere(['!=', "id", $partner->f_user_id])
                    ->andWhere(['phone' => $this->phone])->exists())
                    throw new BadRequestHttpException('Phone was already used for fuser');
                if (\backapi\models\User::find()
                    ->where(['not', ['partner_id' => $this->id]])
                    ->andWhere(['phone_number' => $this->phone])->exists())
                    throw new BadRequestHttpException('Phone was already used for user');

                $fuser = $partner->fUser;
                if (is_null($fuser))
                {
                    $fuser = new User();
                    $fuser->role = User::ROLES['partner'];
                }
                $fuser->phone = $this->phone;
                $fuser->status = $this->status == Partner::STATUS['active'] ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
                if (!empty($this->password) and !empty($this->password_repeat))
                    $fuser->setPassword($this->password);
                $fuser->save();

                $user = $partner->user;
                if (is_null($user))
                {
                    $user = new \backapi\models\User();
                    $user->created_at = time();
                }

                $user->username = $this->phone;
                $user->status = \backapi\models\User::STATUS_ACTIVE;
                $user->generateAuthKey();
                $user->email = "default@partner.com";
                $user->updated_at = time();
                if (!empty($this->password) and !empty($this->password_repeat))
                    $user->setPassword($this->password);
                $user->save();

                if (empty($partner->user))
                {
                    $auth = Yii::$app->authManager;
                    if (!($auth_item = $auth->getRole(Partner::PARTNER_ROLE_NAME)))
                    {
                        $auth_item = $auth->createRole(Partner::PARTNER_ROLE_NAME);
                        $auth->add($auth_item);
                    }
                    $auth->assign($auth_item, $user->id);
                }
            }
            $partner->name = $this->name;
            $partner->contract_number = $this->contract_number;
            $partner->updated_at = time();
            $partner->status = $this->status;
            if (!empty($this->phone))
            {
                $partner->service_amount = $this->service_amount;
                $partner->hook_url = $this->hook_url;
            }
            $partner->f_user_id = $fuser->id ?? null;
            if ($this->image != null)
            {
                $old_image_path = Yii::getAlias('@frontend') . "/web/uploads/partners/" . $partner->image;
                if (file_exists($old_image_path))
                    unlink($old_image_path);
                $partner->image = 'partner' . $partner->updated_at . '.' . $this->image->extension;
                $this->image->saveAs(Yii::getAlias('@frontend') . "/web/uploads/partners/" . $partner->image);
            }

            if (!empty($this->travel_offer_file))
            {
                $directoryPath = Yii::getAlias('@frontend') . "/web/uploads/partners/travel_offer_file/";

                $old_file_path = $directoryPath . $partner->travel_offer_file;
                if (file_exists($old_file_path))
                    unlink($old_file_path);

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