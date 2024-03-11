<?php

namespace backapi\controllers;

use backapi\models\forms\userForms\CreateForm;
use backapi\models\forms\userForms\UpdateForm;
use backapi\models\searchs\UserSearch;
use backapi\models\forms\userForms\LoginForm;
use backapi\models\forms\userForms\UpdatePasswordForm;
use backapi\models\forms\userForms\UpdateProfileForm;
use common\helpers\GeneralHelper;
use mdm\admin\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

class UserController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'login' => ['POST'],
                'create' => ['POST'],
                'update' => ['PUT'],
                'update-profile' => ['PUT'],
                'update-password' => ['PUT'],
                'delete' => ['DELETE'],
            ],
        ];

        $behaviors['authenticator']['except'] = ["login"];

        return $behaviors;
    }

    private function getAttributes()
    {
        return [
            \mdm\admin\models\User::className() => [
                'id',
                'name' => function($model){
                    return $model->first_name . ' ' . $model->last_name;
                },
                'username',
                'email',
                'status',
                'created_at' => function($model){
                    return date('d.m.Y', $model->created_at);
                },
                'updated_at' => function($model){
                    return date('d.m.Y', $model->updated_at);
                },
                'phone_number',
            ]
        ];
    }

    /**
     * @OA\Post(
     *     path="/user/create",
     *     summary="create new user",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"password", "username", "password_repeat", "status"},
     *                 @OA\Property (property="username", type="string", example="username will be used for login"),
     *                 @OA\Property (property="first_name", type="string", example="first name of user"),
     *                 @OA\Property (property="last_name", type="string", example="last name of user"),
     *                 @OA\Property (property="email", type="string", example="email of user"),
     *                 @OA\Property (property="password", type="string", example="password"),
     *                 @OA\Property (property="password_repeat", type="string", example="password"),
     *                 @OA\Property (property="status", type="iteger", example=1, description="10=> active, 9 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created user",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/admin_panel_users")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        $model = new CreateForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/user/update",
     *     summary="update user",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id", "username", "status"},
     *                 @OA\Property (property="id", type="integer", example=2),
     *                 @OA\Property (property="name", type="string", example="nickname"),
     *                 @OA\Property (property="username", type="string", example="Jobir Yusupov"),
     *                 @OA\Property (property="status", type="iteger", example=10, description="10 => active,9 => inactive"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated user",
     *          @OA\JsonContent( type="object", ref="#/components/schemas/admin_panel_users")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/user/all",
     *     summary="Method to get all admin panel users",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from username, first_name, last_name, email"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0 => inactive, 10 => active"),
     *     @OA\Parameter (name="page", in="query", @OA\Schema (type="integer|null"), description="page number"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), example="-username", description="order by id, username, name, created_at, email, status, for desc use '-' sign prefix"),
     *     @OA\Response(
     *         response="200_1", description="if send page param with number",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/admin_panel_users")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="if do not send page",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/admin_panel_users")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);
        $models = $dataProvider->getModels();

        return [
            'models' => ArrayHelper::toArray($models, $this->getAttributes()),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/user/export",
     *     summary="Method to get excel contain all admin panel users",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from username, first_name, last_name, email"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="0 => inactive, 10 => active"),
     *     @OA\Parameter (name="page", in="query", @OA\Schema (type="integer|null"), description="page number"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), example="-username", description="order by id, username, name, created_at, email, status, for desc use '-' sign prefix"),
     *     @OA\Response(
     *         response="200", description="string which excel file contain",
     *         @OA\JsonContent(type="string", example="d�2�2sŠm֋�y��8~��X+P)7/8~'Z...")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionExport()
    {
        GeneralHelper::checkPermission();

        return GeneralHelper::export(User::className(), UserSearch::className(), [
            'id' => 'integer',
            'first_name' => 'string',
            'last_name' => 'string',
            'username' => 'string',
            'email' => 'string',
            'status' => 'integer',
            'created_at' => 'string',
            'updated_at' => 'string',
            'phone_number' => 'string',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/user/login",
     *     summary="Method to login admins",
     *     tags={"UserController"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"username", "password"},
     *                 @OA\Property(
     *                      property="username", type="string", example="jobir"
     *                 ),
     *                 @OA\Property(
     *                      property="password", type="string",
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="token"),
     *     @OA\Response(response="422", description="validation error")
     * )
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->setAttributes($this->post);
        if ($model->validate())
            return $model->login();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Get(
     *     path="/user/profile",
     *     summary="Method to get current user profile data",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="current user profile data",
     *         @OA\JsonContent(type="object", ref="#components/schemas/admin_panel_user_profile")
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'address' => $user->address,
            'roles' => array_keys(Yii::$app->authManager->getRolesByUser($user->id)),
            'routes' => array_keys(Yii::$app->authManager->getPermissionsByUser($user->id)),
        ];
    }

    /**
     * @OA\Post (
     *     path="/user/logout",
     *     summary="Method to make current user logout",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="API return true if user successfully loged out",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionLogout()
    {
        /** @var \mdm\admin\models\User $user */
        $user = Yii::$app->user->identity;
        $user->access_token = '';
        $user->save();
        return true;
    }

    /**
     * @OA\Put (
     *     path="/user/update-profile",
     *     summary="Method to update user profile data",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 required={},
     *                 @OA\Property (property="first_name", type="string|null", example="Jobir"),
     *                 @OA\Property (property="last_name", type="string|null", example="Yusupov"),
     *                 @OA\Property (property="address", type="string|null", example="Buxoro vil. Jondor t."),
     *                 @OA\Property (property="phone_number", type="string|null", example="998946464400"),
     *                 @OA\Property (property="email", type="string|null", example="jobiryusupov0@gmail.com"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response="200", description="updated profile data of user",
     *         @OA\JsonContent(type="object", ref="#components/schemas/admin_panel_user_profile")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdateProfile()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateProfileForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put (
     *     path="/user/update-password",
     *     summary="Method to update user profile data",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody (
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 required={"old_password", "password", "password_repeat"},
     *                 @OA\Property (property="old_password", type="string", example="secret"),
     *                 @OA\Property (property="password", type="string", example="secret1"),
     *                 @OA\Property (property="password_repeat", type="string", example="secret1"),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response="200", description="return true if password successfully changed",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdatePassword()
    {
        GeneralHelper::checkPermission();

        $model = new UpdatePasswordForm();
        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete (
     *     path="/user/delete",
     *     summary="Method to delete user by id",
     *     tags={"UserController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#components/parameters/id"),
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

        if (!is_null($id) and is_numeric($id) and (int)$id == $id and $user = \mdm\admin\models\User::findOne($id))
            return $user->delete();

        throw new BadRequestHttpException("ID is incorrect");
    }
}