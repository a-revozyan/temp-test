<?php
namespace backapi\controllers;

use backapi\models\forms\oldOsagoForms\ImportAndSyncWithGrossAccidentForm;
use backapi\models\forms\oldOsagoForms\ImportAndSyncWithGrossForm;
use backapi\models\forms\oldOsagoForms\ImportOldOsagoFromExcelForm;
use common\helpers\GeneralHelper;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class OldOsagoController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'upload' => ['POST'],
                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }


    /**
     * @OA\Post(
     *     path="/old-osago/import",
     *     summary="import old_osago excel file",
     *     tags={"OldOsagoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property (property="excel_file", type="file"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="true",
     *          @OA\JsonContent( type="boolean", example="true")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionImportFile()
    {
        GeneralHelper::checkPermission();

        $model = new ImportOldOsagoFromExcelForm();
        $model->excel_file = UploadedFile::getInstanceByName('excel_file');
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionImportAndSyncWithGross()
    {
        $model = new ImportAndSyncWithGrossForm();
        $model->excel_file = UploadedFile::getInstanceByName('excel_file');
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    public function actionImportAndSyncWithGrossAccident()
    {
        $model = new ImportAndSyncWithGrossAccidentForm();
        $model->excel_file = UploadedFile::getInstanceByName('excel_file');
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

}