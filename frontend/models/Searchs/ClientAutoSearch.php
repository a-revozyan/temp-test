<?php

namespace frontend\models\Searchs;

use common\models\Autobrand;
use common\models\Autocomp;
use common\models\Automodel;
use common\models\ClientAuto;
use Yii;
use yii\data\ActiveDataProvider;

class ClientAutoSearch extends Autobrand
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
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
        $query = ClientAuto::find()
            ->with('autocomp.automodel.autobrand')
            ->where(['f_user_id' => Yii::$app->getUser()->id]);

        $providerConfig = [
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id'
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
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }
}
