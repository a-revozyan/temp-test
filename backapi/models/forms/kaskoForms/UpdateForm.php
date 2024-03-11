<?php
namespace backapi\models\forms\kaskoForms;

use common\helpers\DateHelper;
use common\models\Kasko;
use common\models\KaskoFile;
use common\models\Osago;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;


class UpdateForm extends Model
{
    public $id;
    public $insurer_passport_series;
    public $insurer_passport_number;
    public $insurer_phone;
    public $insurer_address;
    public $insurer_name;
    public $begin_date;
    public $autonumber;
    public $docs;
    public $images;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['insurer_passport_series', 'insurer_passport_number', 'begin_date', 'insurer_phone', 'insurer_address', 'insurer_name', 'autonumber'], 'safe'],
            [['begin_date'], 'date', 'format' => 'php: d.m.Y'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => Kasko::className(), 'targetAttribute' => ['id' => 'id']],
            [['docs', 'images'], 'each', "rule" => ['file', 'skipOnEmpty' => false,  'minFiles' => 1, 'maxSize' => 50 * 1024 * 1024]],
        ];
    }

    private function saveKaskoFiles($files, $type, $kasko)
    {
        $files = array_filter($files, function ($file){
            if (!empty($file->size))
                return true;
        });

        foreach ($files as $file) {
            $folder_path = '/uploads/kasko-car-files/' . "$kasko->id-$kasko->insurer_passport_series$kasko->insurer_passport_number" . '/';
            if (\yii\helpers\FileHelper::createDirectory(Yii::getAlias('@frontend') . '/web/' . $folder_path, 0775, true)) {
                $file_path = $folder_path . $file->baseName . "-" . Yii::$app->security->generateRandomString(5) . "." . $file->extension;
                if ($file->saveAs(Yii::getAlias('@frontend') . '/web/' . $file_path)) {
                    $kasko_file = new KaskoFile();
                    $kasko_file->path = $file_path;
                    $kasko_file->kasko_id = $kasko->id;
                    $kasko_file->type = $type;
                    $kasko_file->save();
                }
            }
        }
    }

    public function save()
    {
        $kasko = Kasko::findOne($this->id);

        $this->saveKaskoFiles($this->images, KaskoFile::TYPE['image'], $kasko);
        $this->saveKaskoFiles($this->docs, KaskoFile::TYPE['doc'], $kasko);

        $kasko->setAttributes(array_merge($kasko->attributes, array_filter($this->attributes)));
        $kasko->begin_date = DateHelper::date_format($this->begin_date, 'd.m.Y', 'm.d.Y');

        $kasko->save();
        return $kasko;
    }

}