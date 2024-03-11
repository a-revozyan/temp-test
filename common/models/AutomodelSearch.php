<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Automodel;
use yii\helpers\VarDumper;

/**
 * AutomodelSearch represents the model behind the search form of `common\models\Automodel`.
 */
class AutomodelSearch extends Automodel
{
    public $auto_risk_type_name;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'autobrand_id'], 'integer'],
            [['name', 'auto_risk_type_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Automodel::find()->with('autoRiskType')
            ->leftJoin('auto_risk_type', 'auto_risk_type_id = auto_risk_type.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'autobrand_id' => $this->autobrand_id,
        ]);

        $query->andFilterWhere(['ilike', 'automodel.name', $this->name]);
        $query->andFilterWhere(['ilike', 'auto_risk_type.name', $this->auto_risk_type_name]);
        return $dataProvider;
    }
}
