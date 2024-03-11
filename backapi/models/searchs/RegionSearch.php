<?php

namespace backapi\models\searchs;

use common\helpers\GeneralHelper;
use common\models\Region;
use common\models\Surveyer;
use yii\data\SqlDataProvider;

class RegionSearch extends Surveyer
{
    public $name;
    public $for_select;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['for_select'], 'boolean'],
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
        $this->setAttributes($params);
        $lang = GeneralHelper::lang_of_local();
        $query = Region::find();
        if ($this->for_select)
            $query->select(["id", "name_$lang as name"]);

        $providerConfig = [
            'sql' => $query->createCommand()->sql,
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'id', 'name_ru', 'name_uz', 'name_en',
                ],
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ];
        if (is_null(\Yii::$app->request->get('page')))
            $providerConfig['pagination'] = false;

        $dataProvider = new SqlDataProvider($providerConfig);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
             return $dataProvider;
        }


        $params = [];
        if (!is_null($this->name))
        {
            $params['name'] = "%" . $this->name . "%";
            $query->andWhere([
                'or',
                'name_uz ilike :name',
                'name_ru ilike :name',
                'name_en ilike :name',
            ]);
        }

        $dataProvider->sql = $query->createCommand()->sql;
        $dataProvider->params = $params;

        return $dataProvider;
    }
}
