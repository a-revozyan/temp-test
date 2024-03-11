<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "autocomp".
 *
 * @property int $id
 * @property int $automodel_id
 * @property string $name
 * @property float $price
 * @property int $production_year
 * @property int $status
 *
 * @property Automodel $automodel
 * @property Kasko[] $kaskos
 */
class Autocomp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'autocomp';
    }

    public const status = [
        'active' => 1,
        'inactive' => 0,
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['automodel_id', 'name', 'price', 'status'], 'required'],
            [['automodel_id'], 'default', 'value' => null],
            [['automodel_id', 'status'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['automodel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Automodel::className(), 'targetAttribute' => ['automodel_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
        $fields[] = 'automodel';
        $fields['partners'] = function ($model){
            return ArrayHelper::toArray($model->partners, [
                Partner::className() => ['id', 'name']
            ]);
        };

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'automodel_id' => Yii::t('app', 'Automodel ID'),
            'name' => Yii::t('app', 'Name'),
            'price' => Yii::t('app', 'Price'),
        ];
    }

    /**
     * Gets query for [[Automodel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAutomodel()
    {
        return $this->hasOne(Automodel::className(), ['id' => 'automodel_id']);
    }

    /**
     * Gets query for [[Kaskos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKaskos()
    {
        return $this->hasMany(Kasko::className(), ['autocomp_id' => 'id']);
    }

    public function getPartners()
    {
        return $this->hasMany(Partner::className(), ['id' => 'partner_id'])
            ->viaTable('autocomp_partner', ['autocomp_id' => 'id']);
    }

    public static function getWithParentCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getWithParentArr();
        }

        return $_models;
    }

    public function getShortArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function getWithParentArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'status' => $this->status,
            'production_year' => $this->production_year,
            'automodel' => !is_null($this->automodel) ? $this->automodel->getWithParent() : null,
            'partners' => !is_null($this->partners) ? Partner::getForIdNameArrCollection($this->partners) : null,
        ];
    }

    public function getWithOnlyParentArr()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'automodel' => !is_null($this->automodel) ? $this->automodel->getWithParent() : null,
        ];
    }
}
