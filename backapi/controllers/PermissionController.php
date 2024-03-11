<?php

namespace backapi\controllers;

use backapi\models\forms\permissionForms\CreatePermissionForm;
use backapi\models\forms\permissionForms\UpdatePermissionForm;
use backapi\models\searchs\PermissionSearch;
use common\helpers\GeneralHelper;
use common\models\AuthItem;
use mdm\admin\components\Configs;
use mdm\admin\components\Helper;
use mdm\admin\models\User;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class PermissionController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'update' => ['PUT'],
                'delete' => ['DELETE'],
                'assign' => ['POST'],
                'remove' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/permission/all",
     *     summary="Method to get all permissions with or without pagination ",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from description and name"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: created_at, name. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="permissions with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/role")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="permissions without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/role")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $models = $dataProvider->getModels();
        $models = ArrayHelper::toArray($models, [
            AuthItem::className() => [
                'name', 'description', 'created_at'
            ]
        ]);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Post(
     *     path="/permission/create",
     *     summary="create new permission",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property (property="name", type="string", example="new_permission"),
     *                 @OA\Property (property="description", type="string", example="desc"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created permission",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/role")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreatePermissionForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/permission/update",
     *     summary="edit permission",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/josn",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property (property="id", type="string", example="test2", description="o'zgartirmoqchi bo'lgan permission name ini yuborish kerak"),
     *                 @OA\Property (property="name", type="string", example="new_permission"),
     *                 @OA\Property (property="description", type="string", example="desc"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created permission",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/role")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdatePermissionForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save($this->findModel($this->put['id']));

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete  (
     *     path="/permission/delete",
     *     summary="Method to delete permission by id",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (in="query", name="id", @OA\Schema (type="string")),
     *     @OA\Response(
     *         response="200", description="return 1 if successfully deleted",
     *         @OA\JsonContent(type="integer", example=1)
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     * )
     */
    public function actionDelete($id)
    {
        GeneralHelper::checkPermission();

        $model = $this->findModel($id);
        Configs::authManager()->remove($model->item);
        Helper::invalidate();

        return 1;
    }

    /**
     * @OA\Get(
     *     path="/permission/get-by-id",
     *     summary="get permission by id",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (in="query", name="id", @OA\Schema (type="string")),
     *     @OA\Response(
     *         response="200", description="permission users, available, assigned lar ham qo'shilib keladi. ishlatib ko'rish kerak",
     *         @OA\JsonContent(type="object", ref="#components/schemas/role")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        $models = User::find()->where(['in', 'id', \Yii::$app->authManager->getUserIdsByRole($id)])->all();
        $users = ArrayHelper::toArray($models, [
            User::className() => [
                'id', 'username', 'name' => fn($model) => $model->first_name . " " .  $model->last_name
            ]
        ]);
        $model = $this->findModel($id);

        return array_merge(
            $this->findModel($id)->toArray(),
            ['users' => $users],
            $model->getItems(),
        );
    }

    /**
     * @OA\Post(
     *     path="/permission/assign",
     *     summary="assign permission, role or route to permission",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (property="id", type="string", example="role", description="name of permission"),
     *                 @OA\Property (property="items[]", type="array", @OA\Items(type="string", example="test"), description="role name or route or permission, /site/travel-view"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="available, assigned, success",
     *          @OA\JsonContent( type="object",
     *               @OA\Property(property="available", type="object", example="{'/accident-partner-program/*': 'route', 'kasko': 'permission',  'admin': 'role'}"),
     *               @OA\Property(property="assigned", type="object", example="{'/accident-partner-program/*': 'route', 'kasko': 'permission', 'admin': 'role'}"),
     *               @OA\Property(property="success", type="integer", example=1),
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAssign()
    {
        GeneralHelper::checkPermission();

        $id = $this->post['id'];
        $items = $this->post['items'] ?? [];
        $model = $this->findModel($id);
        $success = $model->addChildren($items);
        return array_merge($model->getItems(), ['success' => $success]);
    }

    /**
     * @OA\Post(
     *     path="/permission/remove",
     *     summary="remove permission, role or route from permission",
     *     tags={"PermissionController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (property="id", type="string", example="role", description="name of permission"),
     *                 @OA\Property (property="items[]", type="array", @OA\Items(type="string", example="test"), description="role name or route or permission, /site/travel-view"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="available, assigned, success",
     *          @OA\JsonContent( type="object",
     *               @OA\Property(property="available", type="object", example="{'/accident-partner-program/*': 'route', 'kasko': 'permission',  'admin': 'role'}"),
     *               @OA\Property(property="assigned", type="object", example="{'/accident-partner-program/*': 'route', 'kasko': 'permission', 'admin': 'role'}"),
     *               @OA\Property(property="success", type="integer", example=1),
     *          )
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionRemove()
    {
        GeneralHelper::checkPermission();

        $id = $this->post['id'];
        $items = $this->post['items'] ?? [];
        $model = $this->findModel($id);
        $success = $model->removeChildren($items);

        return array_merge($model->getItems(), ['success' => $success]);
    }

    /**
     * @param $id
     * @return \mdm\admin\models\AuthItem
     * @throws NotFoundHttpException
     */
    protected function findModel($id): \mdm\admin\models\AuthItem
    {
        $auth = Configs::authManager();
        $item = $auth->getPermission($id);
        if ($item) {
            return new \mdm\admin\models\AuthItem($item);
        } else {
            throw new NotFoundHttpException('Permission not found');
        }
    }
}