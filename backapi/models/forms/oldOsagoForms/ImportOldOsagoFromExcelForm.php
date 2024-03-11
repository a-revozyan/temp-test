<?php
namespace backapi\models\forms\oldOsagoForms;

use common\helpers\DateHelper;
use common\models\Autobrand;
use common\models\OldOsago;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class ImportOldOsagoFromExcelForm extends Model
{
    public $excel_file;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['excel_file'], 'required'],
            [['excel_file'], 'file'],
        ];
    }

    public function save()
    {
        $file = $this->excel_file->tempName;
        $file_open = fopen($file,"r");

        $old_osago_arr = [];
        $csv = fgetcsv($file_open, 1000, ",");
        while(($csv = fgetcsv($file_open, 1000, ",")) !== false)
        {
            $status = 0;
            if ($csv[7] == 'Paid')
                $status = 1;

            $old_osago_arr[] = [
                $csv[0],
                DateHelper::date_format($csv[1], 'd.m.Y H:i:s', 'Y-m-d H:i:s'),
                $csv[2],
                $csv[3],
                $csv[4],
                $csv[5],
                $csv[6],
                $status,
                $csv[8],
                date('Y-m-d H:i:s'),
            ];
        }

        Yii::$app->db->createCommand()->batchInsert('old_osago', [
            'old_id',
            'created_at',
            'insurer_name',
            'policy_number',
            'insurer_phone_number',
            'owner',
            'amount_uzs',
            'status',
            'payment_type',
            'imported_at',
        ], $old_osago_arr)->execute();

        return true;
    }

}