<?php

namespace frontend\controllers;

use common\helpers\DateHelper;
use common\models\Accident;
use frontend\models\Searchs\AccidentSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class AccidentController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['Verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
//                'step1' => ['POST'],
            ]
        ];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/accident/get-by-id",
     *     summary="get accident which is created current user by id",
     *     tags={"AccidentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Parameter (name="accident_id", in="query", @OA\Schema (type="integer"), example=270),
     *     @OA\Response(
     *         response="200", description="accident object",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetById(int $accident_id)
    {
        return $this->getByID($accident_id);
    }


    /**
     * @OA\Get(
     *     path="/accident/accidents-of-user",
     *     summary="get accidents which is created current user and payed, waiting policy, received policy status",
     *     tags={"AccidentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/language"),
     *     @OA\Response(
     *         response="200", description="accident",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/accident_in_profile")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionAccidentsOfUser()
    {
        $searchModel = new AccidentSearch();
        $dataProvider = $searchModel->search([
            'status' => [Accident::STATUS['payed'], Accident::STATUS['waiting_for_policy'], Accident::STATUS['received_policy']],
            'f_user_id' => Yii::$app->user->id,
        ]);
        $models = $dataProvider->getModels();
        $models = ArrayHelper::toArray($models, [
            Accident::className() => [
                'id',
                'policy_pdf_url',
                'policy_number',
                'payed_date' => function($model){
                    if (is_null($model->payed_date))
                        return null;
                    return DateHelper::date_format($model->payed_date, 'Y-m-d H:i:s', 'd.m.Y H:i');
                },
            ]
        ]);
        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    private function getById($accident_id)
    {
        if (!$accident = Accident::findOne(['id' => $accident_id, 'f_user_id' => \Yii::$app->user->id]))
            throw new NotFoundHttpException(Yii::t('app', 'accident_id not found'));

        return $accident;
    }
}