<?php

namespace common\helpers;

use common\models\CarInspection;
use common\models\CvatLabel;
use common\models\Kasko;
use common\models\Partner;
use common\models\StatusHistory;
use kartik\mpdf\Pdf;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class PdfHelper
{
    public static function genKaskoPolicyPdf($id)
    {
        $model = Kasko::findOne([$id]);

        $view_pdf = '@frontend/views/product/kaskopdf2';
        if ($model->partner_id == 1)
            $view_pdf = '@frontend/views/product/kaskopdf';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts

            'destination' => Pdf::DEST_STRING,
            'filename' => $model->policy_number . '.pdf',
            'content' => Yii::$app->controller->renderPartial($view_pdf, ['id' => $id]),
            //'cssFile' => '@frontend/web/css/pdf.css',
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '@font-face {
  font-family: "SFProDisplay";
  src: url("/fonts/SFProDisplay-Regular.ttf");
  font-style: normal;
  font-weight: normal;
}
body  {
  font-family: SFProDisplay;
 /* background-color: #fff;*/
}
.pdf .header {
    
    font-size: 20px;
}
.pdf .upper {
    text-transform: uppercase;
}
.pdf th {
    background-color: #efefef;
}
.divtable .divcell {
    border: 1px solid #dee2e6;
    padding: 0.75rem !important;
}
.divtable .brn {
    border-right: none;
}
.divtable .btopn {
    border-top: none;
}
td,th {
    padding: 8px;
}
.pdf h3, .pdf h4 {
    color: #003574;
}
.pechat {
    margin-top: -120px;
    width: 150px;
    margin-right: -50px;
}
.podpis {
    margin-top: -50px;
    margin-left: -104px;
    width: 100%;
}
.assistance {
    font-size: 14px;
    font-weight: bold;
    border-bottom: 1px solid #dee2e6;
}
.assistance .round-flag{
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 10px;
    margin-right: 5px;
}
.assistance h5 {
    color: #212529;
}
.assistance .contacts .c-icon{
    display: inline-block;
    width: 24px;
    height: 24px;
    border-radius: 12px;
    margin-right: 5px;
    border: 1px solid #212529;
    text-align: center;
    padding-top: 2px;
}',
            // any css to be embedded if required
            'options' => [
                // any mpdf options you wish to set
            ],
            'methods' => [
                /*'SetTitle' => 'Privacy Policy - Krajee.com',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['Krajee Privacy Policy||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'Kartik Visweswaran',
                'SetCreator' => 'Kartik Visweswaran',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',*/
            ]
        ]);
        if ($model->partner_id != 1)
        {
            $pdf->marginBottom = 0;
            $pdf->marginTop = 0;
            $pdf->marginLeft = 0;
            $pdf->marginRight = 0;
        }

        return $pdf;
    }

    public static function genAktOsmotr($id, $image_paths = [], $viewingAngles = [])
    {
        $labels = CvatLabel::find()->asArray()->all();
        $labels = ArrayHelper::map($labels, 'label_id', 'color');
        $label_objs = CvatLabel::find()->orderBy('id')->all();
        $car_inspection = CarInspection::findOne($id);
        $partner_logo = Partner::findOne(1)->image;

        /** @var StatusHistory $uploaded_status_history */
        $uploaded_status_history = StatusHistory::find()->where([
            'model_class' => CarInspection::className(),
            'model_id' => $car_inspection->id,
            'to_status' => CarInspection::STATUS['uploaded'],
        ])->orderBy('id desc')->one();
        $uploaded_date = !empty($uploaded_status_history) ? DateHelper::date_format($uploaded_status_history->created_at, 'Y-m-d H:i:s', 'd.m.Y H:i:s') : "";

        $view_pdf = '@frontend/views/pdf/akt_osmotr';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts

            'destination' => Pdf::DEST_STRING, //DEST_BROWSER
            'filename' => $car_inspection->autonumber . '.pdf',
            'content' => Yii::$app->controller->renderPartial($view_pdf, [
                'partner_logo' => $partner_logo,
                'image_paths' => $image_paths,
                'viewing_angles' => $viewingAngles,
                'labels' => $labels,
                'label_objs' => $label_objs,
                'car_inspection' => array_merge($car_inspection->getShortArr(), ['uploaded_date' => $uploaded_date]),
            ]),
            //'cssFile' => '@frontend/web/css/pdf.css',
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '

ol {
  list-style-type: none;
  counter-reset: li;
  padding: 0;
}

li {
  display: inline-block;
  margin-bottom: 10px;
  width: 50%;
  float:left
}

.item-text {
  color: #171717;
  font-size: 12px;
  margin: 0;
  padding: 0;
  padding-left: 6px;
  margin-left:20px;
}

.item-num {
  width: 18px;
  height: 18px;
  border-radius: 18px;
  color: #fff;
  text-align: center;
  font-size: 10px;
  font-weight: 500;
  float:left;
}

.item-wrapper {
  border-bottom: 1px solid #808080;
  padding-bottom: 4px;
  width: 300px;
}

',
            // any css to be embedded if required
            'options' => [
                // any mpdf options you wish to set
            ],
            'methods' => [
                /*'SetTitle' => 'Privacy Policy - Krajee.com',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['Krajee Privacy Policy||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'Kartik Visweswaran',
                'SetCreator' => 'Kartik Visweswaran',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',*/
            ]
        ]);

        $pdf->marginBottom = 20;
        $pdf->marginTop = 20;
        $pdf->marginLeft = 10;
        $pdf->marginRight = 10;

        return $pdf;
    }
}