<?php
namespace backapi\models\forms\bridgeCompanyForms;

use common\models\BridgeCompany;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\VarDumper;


class CreateUpdateBridgeCompanyForm extends Model
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
    public $success_webhook_url;
    public $error_webhook_url;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'status', 'name', 'code', 'success_webhook_url', 'error_webhook_url'], 'required'],
            [['success_webhook_url', 'error_webhook_url'], 'string'],
            ['password', 'required', 'when' => function($model){
                return \Yii::$app->request->isPost;
            },  'whenClient' => "function (attribute, value) {
                return " . \Yii::$app->request->isPost . ";
            }"],
            [['phone_number', 'last_name', 'first_name', 'id'], 'safe'],
            [['status'], 'in', 'range' => [0,10]],
            [['email', 'username'], 'uniqueInUsers'],
            [['code'], 'uniqueInBridgeCompanies'],
        ];
    }

    public function uniqueInUsers($attribute, $params)
    {
        $data = \Yii::$app->request->post();
        if (\Yii::$app->request->isPut)
            $data = (array)json_decode(\Yii::$app->request->rawBody);

        $id = $data['id'] ?? null;
        $val = $data[$attribute] ?? null;
        if (is_null($id) and \mdm\admin\models\User::find()->where([$attribute => $val])->exists())
            $this->addError($attribute, Yii::t('app',  '{attribute}! oldin kiritilgan', [
                'attribute' => $attribute
            ]));

        if (!is_null($id) and \mdm\admin\models\User::find()->where([$attribute => $val])->andWhere(['not', ['id' => BridgeCompany::findOne($id)->user_id]])->exists())
            $this->addError($attribute, Yii::t('app',  '{attribute}! oldin kiritilgan', [
                'attribute' => $attribute
            ]));
    }

    public function uniqueInBridgeCompanies($attribute, $params)
    {
        $data = \Yii::$app->request->post();
        if (\Yii::$app->request->isPut)
            $data = (array)json_decode(\Yii::$app->request->rawBody);

        $id = $data['id'] ?? null;
        $val = $data[$attribute] ?? null;
        if (is_null($id) and BridgeCompany::find()->where([$attribute => $val])->exists())
            $this->addError($attribute, Yii::t('app',  '{attribute}! oldin kiritilgan', [
                'attribute' => $attribute
            ]));

        if (!is_null($id) and BridgeCompany::find()->where([$attribute => $val])->andWhere(['not', ['id' => $id]])->exists())
            $this->addError($attribute, Yii::t('app',  '{attribute}! oldin kiritilgan', [
                'attribute' => $attribute
            ]));
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
            $bridge_company->success_webhook_url = $this->success_webhook_url;
            $bridge_company->error_webhook_url = $this->error_webhook_url;
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
                $auth = \Yii::$app->authManager;
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
                return $bridge_company;
            }
            else
            {
                $transaction->rollBack();
                return false;
            }

        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}