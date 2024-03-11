<?php

namespace backapi\models\searchs;

use common\models\Kasko;
use common\models\Surveyer;
use yii\data\SqlDataProvider;

class SurveyerSearch extends Surveyer
{
    public $region_id;
    public $status;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'status'], 'integer'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return SqlDataProvider
     */
    public function search($params)
    {
        $query = Surveyer::find()
            ->leftJoin('auth_assignment', '"user"."id" = CAST("auth_assignment"."user_id" AS INTEGER)')
            ->select([
                "user.id as id",
                "user.first_name as first_name",
                "user.last_name as last_name",
                'region.name_ru as region_name',
                "user.created_at",
                "kasko.count as kasko_count",
                "kasko.average_processed_time as average_processed_time",
                "phone_number",
                "status",
            ])
            ->leftJoin('region', '"user"."region_id" = region.id')
            ->leftJoin(
                [
                    "kasko" => Kasko::find()->select([
                        "count(kasko.id) as count",
                        'surveyer_id',
                        'sum(processed_date - payed_date) / (count(kasko.id) * 60 * 60) as average_processed_time',
                    ])
                        ->groupBy('surveyer_id')
                ],
                '"kasko"."surveyer_id" = "user"."id"'
            )
            ->where('auth_assignment.item_name = :role');

        $providerConfig = [
            'sql' => $query->createCommand()->sql,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'created_at', 'kasko_count', 'average_processed_time', 'phone_number',
                ],
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new SqlDataProvider($providerConfig);

        $this->setAttributes($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
//             $query->where('0=1');
             return $dataProvider;
        }


        $params = [
            'role' => Surveyer::SURVEYER_ROLE_NAME
        ];
        if (!is_null($this->region_id))
        {
            $params['region_id'] = $this->region_id;
            $query->andWhere('region.id = :region_id');
        }

        if (!is_null($this->status))
        {
            $params['status'] = $this->status;
            $query->andWhere('status = :status');
        }


        $dataProvider->sql = $query->createCommand()->sql;
        $dataProvider->params = $params;

        return $dataProvider;
    }
}
