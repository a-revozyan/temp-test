<?php

namespace backapi\models\searchs;

use common\models\KaskoRisk;
use common\models\KaskoTariff;
use yii\data\SqlDataProvider;

class KaskoTariffSearch extends KaskoTariff
{
    public $partner_name;
    public $partner_id;
    public $name;
    public $risks_count;
    public $search;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['risks_count', 'partner_id'], 'integer'],
            [['search', 'partner_name', 'name'], 'string'],
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
        $query = KaskoTariff::find()
            ->select([
                "kasko_tariff.id",
                "kasko_tariff.name as name",
                "partner.name as partner_name",
                "partner.id as partner_id",
                "risks_count",
                "amount",
                "min_price",
                "max_price",
                "min_year",
                "max_year",
            ])
            ->leftJoin(
                [
                    'risks_by_tariff' => KaskoRisk::find()
                        ->select(['count(kasko_risk.id) as risks_count', 'kasko_tariff_risk.tariff_id'])
                        ->leftJoin('kasko_tariff_risk', 'kasko_tariff_risk.risk_id = kasko_risk.id')
                        ->groupBy('kasko_tariff_risk.tariff_id')
                ],
                'risks_by_tariff.tariff_id = kasko_tariff.id'
            )
            ->joinWith(['partner']);

        $providerConfig = [
            'sql' => $query->createCommand()->sql,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'name', 'partner_name', 'risks_count',
                ],
                'defaultOrder' => ['name' => SORT_ASC]
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


        $params = [];
        if (!is_null($this->partner_id))
        {
            $params['partner_id'] = $this->partner_id;
            $query->andWhere('partner.id = :partner_id');
        }

        if (!is_null($this->search))
        {
            $params['search'] = "%" . $this->search . "%";
            $query->andWhere("partner.name ilike :search or kasko_tariff.name ilike :search");
        }

        $dataProvider->sql = $query->createCommand()->sql;
        $dataProvider->params = $params;

        return $dataProvider;
    }
}
