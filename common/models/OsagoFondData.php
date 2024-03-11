<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "osago_fond_data".
 *
 * @property int $id
 * @property int|null $osago_id
 * @property int|null $marka_id
 * @property int|null $model_id
 * @property string|null $model_name
 * @property int|null $vehicle_type_id
 * @property string|null $tech_passport_issue_date
 * @property int|null $issue_year
 * @property string|null $body_number
 * @property string|null $engine_number
 * @property int|null $use_territory
 * @property int|null $fy
 * @property string|null $last_name_latin
 * @property string|null $first_name_latin
 * @property string|null $middle_name_latin
 * @property int|null $oblast
 * @property int|null $rayon
 * @property int|null $ispensioner
 * @property string|null $orgname
 */
class OsagoFondData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'osago_fond_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['osago_id', 'marka_id', 'model_id', 'vehicle_type_id', 'issue_year', 'use_territory', 'fy', 'oblast', 'rayon', 'ispensioner'], 'default', 'value' => null],
            [['osago_id', 'marka_id', 'model_id', 'vehicle_type_id', 'issue_year', 'use_territory', 'fy', 'oblast', 'rayon', 'ispensioner'], 'integer'],
            [['model_name', 'tech_passport_issue_date', 'body_number', 'engine_number', 'last_name_latin', 'first_name_latin', 'middle_name_latin', 'orgname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'osago_id' => 'Osago ID',
            'marka_id' => 'Marka ID',
            'model_id' => 'Model ID',
            'model_name' => 'Model Name',
            'vehicle_type_id' => 'Vehicle Type ID',
            'tech_passport_issue_date' => 'Tech Passport Issue Date',
            'issue_year' => 'Issue Year',
            'body_number' => 'Body Number',
            'engine_number' => 'Engine Number',
            'use_territory' => 'Use Territory',
            'fy' => 'Fy',
            'last_name_latin' => 'Last Name Latin',
            'first_name_latin' => 'First Name Latin',
            'middle_name_latin' => 'Middle Name Latin',
            'oblast' => 'Oblast',
            'rayon' => 'Rayon',
            'ispensioner' => 'Ispensioner',
            'orgname' => 'Orgname',
        ];
    }
}
