<?php
namespace backapi\models\forms\oldOsagoForms;

use common\models\GrossAuto;
use common\models\Osago;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class ImportAndSyncWithGrossForm extends Model
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

        $gross_osago_arr = [];
        $csv = fgetcsv($file_open, 1000, ";");
        $csv = fgetcsv($file_open, 1000, ";");
        while(($csv = fgetcsv($file_open, 1000, ";")) !== false)
        {
            $gross_osago_arr[] = preg_replace('/\s+/', ' ', $csv[4] ?? '');
//            $gross_auto = GrossAuto::findOne(['name' => trim($csv[6] ?? '')]);
//            if (is_null($gross_auto))
//            {
//                $gross_auto = new GrossAuto();
//                $gross_auto->name = trim($csv[6] ?? '');
//                $gross_auto->created_at = date('Y-m-d H:i:s');
//                $gross_auto->save();
//            }
//            Osago::updateAll(['gross_auto_id' => $gross_auto->id], ['policy_number' => $csv[4]]);
        }

        $our_osago_arr = Osago::find()->select(['id', 'policy_number'])
            ->andWhere(['status' => Osago::STATUS['received_policy']])
            ->andWhere(['in', 'policy_number', $gross_osago_arr])->all();
        $our_osago_arr = ArrayHelper::map($our_osago_arr, 'id', 'policy_number');

        $diff_policies = array_diff($gross_osago_arr, $our_osago_arr);
        VarDumper::dump($diff_policies);
        $diff_osagos = Osago::find()->select(['id', 'status', 'policy_number', 'policy_pdf_url'])->where(['in', 'policy_number', $diff_policies])->asArray()->all();
        VarDumper::dump($diff_osagos); die();

//        VarDumper::dump($our_osago_arr); die();
//        VarDumper::dump($gross_osago_arr); die();

        return true;
    }

}