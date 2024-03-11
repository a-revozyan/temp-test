<?php
namespace backapi\models\forms\agentForms;

use common\models\Agent;
use common\models\AgentFile;
use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\VarDumper;


class UpdateAgentForm extends Model
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
    public $agent_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['inn'], 'safe'],
            [['first_name', 'last_name', 'contract_number', 'status', 'phone', 'agent_id'], 'required'],
            [['status', 'agent_id'], 'integer'],
            [['status'], 'in', 'range'=> [9,10]],
            [['files'], 'each', 'rule' => ['file', 'skipOnEmpty' => false]],
            [['files'], 'max5'],
            [['logo'], 'file', 'skipOnEmpty' => true],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::className(), 'targetAttribute' => ['agent_id' => 'id']],
            ['phone', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'phone', 'filter' => function($query){
                $query->andWhere(['not', ['id' => Agent::findOne($this->agent_id)->f_user_id ?? -1]]);
            }],
        ];

        if (!is_null(\Yii::$app->request->post('password')))
        {
            $rules[] = [['repeat_password'], 'required'];
            $rules[] = ['password', 'compare', 'compareAttribute' => 'repeat_password'];
        }

        return $rules;
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
            $agent = Agent::findOne($this->agent_id);
            $user = $agent->user;
            $user->first_name = $this->first_name;
            $user->last_name = $this->last_name;
            $user->status = $this->status;
            $user->phone = $this->phone;
            $user->updated_at = time();
            if (!is_null($this->password))
                $user->setPassword($this->password);
            $user->save();

            $agent->contract_number = $this->contract_number;
            $agent->inn = $this->inn;
            $agent->save();

            if (isset($this->logo))
                $agent = $agent->saveFile($agent, $this->logo, $agent->logo);

            $agent_files = [];
            foreach ($this->files as $file) {
                $agent_file = new AgentFile();
                $agent_file->agent_id = $agent->id;
                $agent_file = $agent_file->saveFile($agent_file, $file, $agent_file->path);
            }

            $transaction->commit();
            return $agent->getShortArr();

        } catch (Exception $e) {
            $transaction->rollBack();
            return $e;
        }
    }

}