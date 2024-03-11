<?php
namespace backapi\models\forms\agentForms;

use common\models\Agent;
use common\models\AgentFile;
use common\models\User;
use yii\base\Exception;
use yii\base\Model;


class CreateAgentForm extends Model
{
    public $first_name;
    public $last_name;
    public $contract_number;
    public $logo;
    public $status;
    public $files;
    public $phone;
    public $password;
    public $repeat_password;
    public $inn;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inn'], 'safe'],
            [['first_name', 'last_name', 'contract_number', 'logo', 'status', 'password', 'repeat_password', 'phone'], 'required'],
            ['phone', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'phone'],
            [['status'], 'integer'],
            [['status'], 'in', 'range'=> [9,10]],
            [['files'], 'each', 'rule' => ['file', 'skipOnEmpty' => false]],
            [['files'], 'max5'],
            [['logo'], 'file', 'skipOnEmpty' => false],
            ['password', 'compare', 'compareAttribute' => 'repeat_password']
        ];
    }

    public function max5()
    {
        if (count($this->files) > 5)
            $this->addError('files','Number of files must not be greater then 5!');
    }

    public function save()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $user = new User();
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->status = $this->status;
            $user->phone = $this->phone;
            $user->role = User::ROLES['user'];
            $user->created_at = time();
            $user->updated_at = time();
            $user->generateAuthKey();
            $user->setPassword($this->password);
            $user->save();

            $agent = new Agent();
            $agent->f_user_id = $user->id;
            $agent->contract_number = $this->contract_number;
            $agent->inn = $this->inn;
            $agent->save();

            if (!empty($this->logo))
                $agent = $agent->saveFile($agent, $this->logo);

            foreach ($this->files as $file) {
                $agent_file = new AgentFile();
                $agent_file->agent_id = $agent->id;
                $agent_file = $agent_file->saveFile($agent_file, $file);
            }

            $transaction->commit();
//            return $agent->toArray();
            return $agent->getShortArr();

        } catch (Exception $e) {
            $transaction->rollBack();
            return $e;
        }
    }

}