<?php

namespace common\models;

use common\helpers\DateHelper;
use Yii;

/**
 * This is the model class for table "client_auto".
 *
 * @property int $id
 * @property int|null $f_user_id
 * @property int|null $autocomp_id
 * @property int|null $manufacture_year
 * @property string|null $autonumber
 * @property string|null $tex_pass_series
 * @property string|null $tex_pass_number
 * @property string|null $created_at
 */
class ClientAuto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_auto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['f_user_id', 'autocomp_id', 'manufacture_year'], 'default', 'value' => null],
            [['f_user_id', 'autocomp_id', 'manufacture_year'], 'integer'],
            [['created_at'], 'safe'],
            [['autonumber', 'tex_pass_series', 'tex_pass_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'f_user_id' => 'F User ID',
            'autocomp_id' => 'Autocomp ID',
            'manufacture_year' => 'Manufacture Year',
            'autonumber' => 'Autonumber',
            'tex_pass_series' => 'Tex Pass Series',
            'tex_pass_number' => 'Tex Pass Number',
            'created_at' => 'Created At',
        ];
    }

    public function getFUser()
    {
        return $this->hasOne(User::className(), ['id' => 'f_user_id']);
    }

    public function getAutocomp()
    {
        return $this->hasOne(Autocomp::className(), ['id' => 'autocomp_id']);
    }

    public static function getFullArrCollection($models)
    {
        $_models = [];
        foreach ($models as $model) {
            $_models[] = $model->getFullArr();
        }

        return $_models;
    }

    public function getFullArr()
    {
        $auto_comp = $this->autocomp;
        return [
            'id' => $this->id,
            'autonumber' => $this->autonumber,
            'tex_pass_series' => $this->tex_pass_series,
            'tex_pass_number' => $this->tex_pass_number,
            'manufacture_year' => $this->manufacture_year,
            'autocomp' => empty($auto_comp) ? null : $auto_comp->getWithOnlyParentArr(),
        ];
    }
}
