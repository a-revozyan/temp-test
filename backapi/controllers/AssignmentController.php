<?php

namespace backapi\controllers;

use backapi\models\searchs\UserSearch;
use common\helpers\GeneralHelper;
use mdm\admin\models\User;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

class AssignmentController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'assign-role-to-user' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    private function getUser($user_id)
    {
        if (is_null($user_id) or !is_numeric($user_id) or (int)$user_id != $user_id or !$user = User::findOne($user_id))
            throw new BadRequestHttpException("User id is incorrect");

        return $user;
    }

    private function getRole($role_name)
    {
        if (!$role = \Yii::$app->authManager->getRole($role_name))
            throw new BadRequestHttpException("Role name is incorrect");

        return $role;
    }

    private function getUserWithRoles($user)
    {
        return array_merge($user->getAttributes(['id', 'username']), [
            'name' => $user->first_name . " " . $user->last_name,
            'roles' => array_values(\Yii::$app->authManager->getRolesByUser($user->id)),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/assignment/all",
     *     summary="Method to get all users(adminkadagi userlar) with or without pagination",
     *     tags={"AssignmentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from username, full name, email"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="string"), description="STATUS_INACTIVE = 0, STATUS_ACTIVE = 10"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), description="values: 'id', 'username', 'name', 'created_at', 'email', 'status'. use '-' for descending"),
     *     @OA\Parameter (name="page", ref="#components/parameters/page"),
     *     @OA\Response(
     *         response="200_1", description="users with pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/admin_panel_user_in_assignment")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="users without pagination",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#/components/schemas/admin_panel_user_in_assignment")),
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
        $models = ArrayHelper::toArray($models, [
            User::className() => [
                'id',
                'name' => function($model){
                    return $model->first_name . ' ' . $model->last_name;
                },
                'username'
            ]
        ]);

        return [
            'models' => $models,
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Get(
     *     path="/assignment/get-by-id",
     *     summary="get user with roles by id",
     *     tags={"AssignmentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (in="query", name="user_id", @OA\Schema (type="integer")),
     *     @OA\Response(
     *         response="200", description="user with roles",
     *         @OA\JsonContent(type="object", ref="#components/schemas/admin_panel_user_with_roles_in_assignment")
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionGetById($user_id)
    {
        GeneralHelper::checkPermission();

        $user = $this->getUser($user_id);
        return $this->getUserWithRoles($user);
    }

    /**
     * @OA\Post(
     *     path="/assignment/assign-role-to-user",
     *     summary="assign role to user",
     *     tags={"AssignmentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (in="query", name="role_name", @OA\Schema (type="string"), example="surveyer", description="name of role"),
     *     @OA\Parameter (in="query", name="user_id", @OA\Schema (type="integer"), example=5, description="id of user"),
     *
     *     @OA\Response(response="200", description="user with roles",
     *          @OA\JsonContent(type="object", ref="#components/schemas/admin_panel_user_with_roles_in_assignment")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAssignRoleToUser($role_name, $user_id)
    {
//        GeneralHelper::checkPermission();

        $role = $this->getRole($role_name);
        $user = $this->getUser($user_id);

        \Yii::$app->authManager->assign($role, $user->id);

        return $this->getUserWithRoles($user);
    }

    /**
     * @OA\Post(
     *     path="/assignment/remove-role-to-user",
     *     summary="remove role from user",
     *     tags={"AssignmentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (in="query", name="role_name", @OA\Schema (type="string"), example="surveyer", description="name of role"),
     *     @OA\Parameter (in="query", name="user_id", @OA\Schema (type="integer"), example=5, description="id of user"),
     *
     *     @OA\Response(response="200", description="user with roles",
     *          @OA\JsonContent(type="object", ref="#components/schemas/admin_panel_user_with_roles_in_assignment")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionRemoveRoleToUser($role_name, $user_id)
    {
        GeneralHelper::checkPermission();

        $role = $this->getRole($role_name);
        $user = $this->getUser($user_id);

        \Yii::$app->authManager->revoke($role, $user->id);

        return $this->getUserWithRoles($user);
    }
}