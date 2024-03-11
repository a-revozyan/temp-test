<?php
namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\View;


class BridgeCompanyForm extends Model
{
    public $id;
    public $name;
    public $code;
    public $username;
    public $email;
    public $status;
    public $password;
    public $phone_number;
    public $last_name;
    public $first_name;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'status', 'name', 'code'], 'required'],
            ['password', 'required', 'when' => function($model){
                return empty(Yii::$app->requestedParams['id']);
            },  'whenClient' => "function (attribute, value) {
                return " . empty(Yii::$app->requestedParams['id']) . ";
            }"],
            [['phone_number', 'last_name', 'first_name'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Company name'),
            'code' => Yii::t('app', 'Company code'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'password' => Yii::t('app', 'Password'),
            'phone_number' => Yii::t('app', 'Phone_number'),
            'last_name' => Yii::t('app', 'last_name'),
            'first_name' => Yii::t('app', 'first_name'),
        ];
    }

    public function save()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $is_update = true;
            if (empty($this->id))
            {
                $is_update = false;
                $bridge_company = new BridgeCompany();
                $bridge_company->created_at = time();
            }else
                $bridge_company = BridgeCompany::findOne(['id' => $this->id]);

            $bridge_company->name = $this->name;
            $bridge_company->code = $this->code;
            $bridge_company->status = $this->status;
            $bridge_company->updated_at = time();

            $bridge_company_saved_successfully = $bridge_company->save();

            $this->id = $bridge_company->id;

            if (!$user = $bridge_company->user)
            {
                $user = new \mdm\admin\models\User();
                $user->created_at = time();
            }
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            $user->phone_number = $this->phone_number;
            $user->last_name = $this->last_name;
            $user->first_name = $this->first_name;
            $user->generateAuthKey();
            if (!empty($this->password))
                $user->setPassword($this->password);

            $user->updated_at = time();
            $user_saved_successfully = $user->save();
            $bridge_company->user_id = $user->id;

            $bridge_company_user_id_saved = $bridge_company->save();

            if ($user_saved_successfully and !$is_update)
            {
                $auth = Yii::$app->authManager;
                if (!($auth_item = $auth->getRole(BridgeCompany::BRIDGE_COMPANY_ROLE_NAME)))
                {
                    $auth_item = $auth->createRole(BridgeCompany::BRIDGE_COMPANY_ROLE_NAME);
                    $auth->add($auth_item);
                }
                $auth->assign($auth_item, $user->id);
            }

            if ($bridge_company_saved_successfully and $user_saved_successfully and $bridge_company_user_id_saved)
            {
                $transaction->commit();
                return true;
            }
            else
            {
                $transaction->rollBack();
                return false;
            }

        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
}
