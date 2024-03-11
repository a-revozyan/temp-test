<?php
namespace backapi\models\forms\Setting;

use common\models\Setting;
use yii\base\Model;
class changeValueForm extends Model
{
    public $id;
    public $value;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'value'], 'required'],
            [['id'], 'integer'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Setting::className(), 'targetAttribute' => ['id' => 'id']],
        ];

    }

    public function save()
    {
        $setting = Setting::findOne($this->id);
        $setting->value = $this->value;
        $setting->updated_at = date('Y-m-d H:i:s');
        $setting->save();

        return $setting;
    }
}