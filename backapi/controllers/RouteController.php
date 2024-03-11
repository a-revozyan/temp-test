<?php

namespace backapi\controllers;

use backapi\models\forms\roleForms\CreateRoleForm;
use backapi\models\forms\roleForms\UpdateRoleForm;
use backapi\models\searchs\RoleSearch;
use common\helpers\GeneralHelper;
use common\models\AuthItem;
use mdm\admin\components\Configs;
use mdm\admin\components\Helper;
use mdm\admin\models\Route;
use mdm\admin\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class RouteController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'assign' => ['POST'],
                'remove' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/route/all",
     *     summary="Method to get all available and assigned routes",
     *     tags={"RouteController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200_1", description="roles with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="available", type="array",  @OA\Items(type="string", example="/agent/statistics")),
     *              @OA\Property (property="assigned", type="object", ref="/osago/change-status")
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $model = new Route();
        return  $model->getRoutes();
    }

    /**
     * @OA\Post(
     *     path="/route/assign",
     *     summary="assign route to database",
     *     tags={"RouteController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (property="routes[]", type="array", @OA\Items(type="string", example="/osago/all")),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created routes",
     *          @OA\JsonContent( type="object",
     *               @OA\Property(property="available", type="array",  @OA\Items(type="string", example="/agent/statistics")),
     *               @OA\Property (property="assigned", type="object", ref="/osago/change-status")
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAssign()
    {
        GeneralHelper::checkPermission();

        $routes = Yii::$app->getRequest()->post('routes', []);
        $model = new Route();
        $model->addNew($routes);
        Yii::$app->getResponse()->format = 'json';
        return $model->getRoutes();
    }

    /**
     * @OA\Post(
     *     path="/route/remove",
     *     summary="remove route from database",
     *     tags={"RouteController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (property="routes[]", type="array", @OA\Items(type="string", example="/osago/all")),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created role",
     *          @OA\JsonContent( type="object",
     *               @OA\Property(property="available", type="array",  @OA\Items(type="string", example="/agent/statistics")),
     *               @OA\Property (property="assigned", type="object", ref="/osago/change-status")
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionRemove()
    {
        GeneralHelper::checkPermission();

        $routes = Yii::$app->getRequest()->post('routes', []);
        $model = new Route();
        $model->remove($routes);
        Yii::$app->getResponse()->format = 'json';
        return $model->getRoutes();
    }

}