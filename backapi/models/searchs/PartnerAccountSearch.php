<?php

namespace backapi\models\searchs;

use common\models\CarInspection;
use common\models\Partner;
use common\models\PartnerAccount;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;

class PartnerAccountSearch extends CarInspection
{
    public $begin_date;
    public $end_date;
    public $partner_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Partner::className(), 'targetAttribute' => ['partner_id' => 'id'],
                'filter' => function($query){
                    return $query->leftJoin('f_user', 'partner.f_user_id = f_user.id')
                        ->andWhere([
                            'f_user.role' => User::ROLES['partner']
                        ]);
                }
            ],
            [['begin_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
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
        $query = PartnerAccount::find()->joinWith(['partner', 'user']);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'created_at', 'amount'
                ],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new ActiveDataProvider($providerConfig);

        $this->setAttributes($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        if (!empty($this->begin_date))
            $this->begin_date = $this->begin_date . " 00:00:00";
        if (!empty($this->end_date))
            $this->end_date = $this->end_date . " 23:59:59";

        $query->andFilterWhere(['partner_account.partner_id' => $this->partner_id]);
        $query->andFilterWhere(['>', 'partner_account.created_at', $this->begin_date]);
        $query->andFilterWhere(['<', 'partner_account.created_at', $this->end_date]);

        return $dataProvider;
    }
}
