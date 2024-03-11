<?php
namespace backapi\models\forms\osagoForms;

use common\models\Osago;
use yii\base\Model;


class UpdateForm extends Model
{
    public $id;
    public $insurer_passport_series;
    public $insurer_passport_number;
    public $insurer_birthday;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['insurer_passport_series', 'insurer_passport_number', 'insurer_birthday'], 'safe'],
            [['insurer_birthday'], 'date', 'format' => 'php: d.m.Y'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Osago::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function save()
    {
        $osago = Osago::findOne($this->id);
        $osago->insurer_passport_series = $this->insurer_passport_series;
        $osago->insurer_passport_number = $this->insurer_passport_number;
        $osago->insurer_birthday = date_create_from_format('d.m.Y', $this->insurer_birthday)->getTimestamp();
        $osago->save();
        return $osago;
    }

}