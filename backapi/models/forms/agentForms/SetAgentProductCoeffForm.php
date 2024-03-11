<?php
namespace backapi\models\forms\agentForms;

use common\models\Agent;
use common\models\AgentProductCoeff;
use common\models\Product;
use yii\base\Model;


class SetAgentProductCoeffForm extends Model
{
    public $product_ids;
    public $coeffs;
    public $agent_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_id', 'product_ids', 'coeffs'], 'required'],
            [['agent_id'], 'integer'],
            [['product_ids'], 'each', 'rule' => ['in', 'range' => Product::products]],
            [['coeffs'], 'each', 'rule' => ['double']],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::className(), 'targetAttribute' => ['agent_id' => 'id']],
        ];
    }

    public function save()
    {
        foreach ($this->product_ids as $key => $product_id) {
            $agent_product_coeff_attrs = ['product_id' => $product_id, 'agent_id' => $this->agent_id];
            $agent_product_coeff = AgentProductCoeff::find()->where($agent_product_coeff_attrs)->one();
            if ($agent_product_coeff == null)
            {
                $agent_product_coeff = new AgentProductCoeff();
                $agent_product_coeff->setAttributes($agent_product_coeff_attrs);
            }
            $agent_product_coeff->coeff = $this->coeffs[$key];
            $agent_product_coeff->save();
        }

        return Agent::findOne($this->agent_id)->getShortArr();
    }

}