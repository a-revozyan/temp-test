<?php
namespace backapi\controllers;

use backapi\models\forms\kaskoForms\ChangeStatusForm;
use backapi\models\forms\kaskoForms\DonwloadPolicyForm;
use backapi\models\forms\kaskoForms\UpdateForm;
use common\helpers\GeneralHelper;
use common\models\KaskoFile;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class KaskoController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'change-status' => ['PUT'],
//                'send-request-to-get-policy' => ['POST'],
                'update' => ['PUT'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Put(
     *     path="/kasko/change-status",
     *     summary="change status kasko",
     *     tags={"KaskoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "status"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="status", type="integer", example="9", description="o'zgartirilishi kerak bo'lgan status. hozircha faqat 9"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionChangeStatus()
    {
        GeneralHelper::checkPermission();

        $model = new ChangeStatusForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/kasko/update",
     *     summary="update kasko",
     *     tags={"KaskoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="autonumber", type="string", example="01Y195DA"),
     *                 @OA\Property (property="begin_date", type="string", example="29.01.2021", description="formt: d.m.Y"),
     *                 @OA\Property (property="insurer_name", type="string", example="Jobir"),
     *                 @OA\Property (property="insurer_address", type="string", example="Buxoro"),
     *                 @OA\Property (property="insurer_phone", type="string", example="998971234567"),
     *                 @OA\Property (property="insurer_passport_series", type="string", example="AB"),
     *                 @OA\Property (property="insurer_passport_number", type="string", example="1234567"),
     *                 @OA\Property (property="images[]", type="array", @OA\Items(type="file"),  description="max 50MB"),
     *                 @OA\Property (property="docs[]", type="array", @OA\Items(type="file"), description="max 50MB"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated kasko",
     *          @OA\JsonContent( type="object")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateForm();
        $model->setAttributes($this->post);
        $model->docs = UploadedFile::getInstancesByName('docs');
        $model->images = UploadedFile::getInstancesByName('images');
        if ($model->validate())
            return $model->save()->getFullArr();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete(
     *     path="/kasko/delete-file",
     *     summary="delete file of kasko by id of file",
     *     tags={"KaskoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="kasko_file_id", in="query", @OA\Schema (type="integer"), example=34),
     *     @OA\Response(
     *         response="200", description="if successfully deleted API return true",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDeleteFile($kasko_file_id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($kasko_file_id) and is_numeric($kasko_file_id) and (int)$kasko_file_id == $kasko_file_id and $file = KaskoFile::findOne($kasko_file_id))
        {
            $root = str_replace('\\', '/', \Yii::getAlias('@frontend') . '/web/');
            if (is_file($root .  $file->path))
                unlink($root .  $file->path);
            $file->delete();
            return true;
        }

        throw new BadRequestHttpException(\Yii::t('app', 'ID is incorrect'));
    }

    /**
     * @OA\Get (
     *     path="/kasko/download-policy",
     *     summary="download policy pdf",
     *     tags={"KaskoController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="kasko_id", in="query", required=true,  @OA\Schema(type="integer"), example=750, description="id of kasko which is created by current user and ready to download(status =7,8)"),
     *
     *     @OA\Response(response="200", description="the string which the pdf consist of",
     *           @OA\JsonContent(type="string", example="JVBERi0xLjQKJeLjz9MKMyAwIG9iago8PC9UeXBlIC9QYWdlCi9QYXJlbnQgMSAwIFI")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDownloadPolicy()
    {
        $model = new DonwloadPolicyForm();
        $model->setAttributes($this->get);
        if ($model->validate())
            return $model->download();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }
}