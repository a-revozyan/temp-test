<?php
namespace backapi\controllers;

use backapi\models\forms\agentForms\CreateAgentForm;
use backapi\models\forms\agentForms\SetAgentProductCoeffForm;
use backapi\models\forms\agentForms\UpdateAgentForm;
use backapi\models\searchs\AgentSearch;
use backapi\models\searchs\KaskoSearch;
use common\helpers\GeneralHelper;
use common\models\Agent;
use common\models\AgentFile;
use common\models\AgentProductCoeff;
use common\models\Kasko;
use common\models\Surveyer;
use common\models\Travel;
use common\models\User;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class AgentController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'create' => ['POST'],
                'set-agent-product-coeff' => ['PUT'],
                'update' => ['POST'],
                'delete' => ['DELETE'],
            ],
        ];
        return $behaviors;
    }

    /**
     * @OA\Get(
     *     path="/agent/statistics",
     *     summary="Method to get all statistic info which in agent page",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Response(
     *         response="200", description="all statistic info",
     *         @OA\JsonContent(type="object",
     *              @OA\Property(property="agent_count", type="integer", example=13),
     *              @OA\Property(property="weekly_policy_count", type="integer", example=1),
     *              @OA\Property(property="weekly_added_agent_count", type="integer", example=2),
     *              @OA\Property(property="monthly_policy_count", type="integer", example=3),
     *              @OA\Property(property="monthly_policy_amount", type="integer", example=4),
     *         )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionStatistics()
    {
        GeneralHelper::checkPermission();

        $agent_count = Agent::find()
            ->leftJoin('f_user', '"agent"."f_user_id" = "f_user"."id"')
            ->where(['not', ['status' => User::STATUS_DELETED]]);

        $query = Agent::find()
            ->leftJoin('f_user', '"agent"."f_user_id" = "f_user"."id"')
            ->select([
                "(coalesce(kasko_count, 0) + coalesce(travel_count,0)) as policy_count",
                "(coalesce(agent_kasko_amount, 0)+coalesce(agent_travel_amount, 0)) as policy_amount",
            ])
            ->leftJoin(
                [
                    "kasko" => Kasko::find()->select([
                        "count(kasko.id) as kasko_count",
                        'f_user_id',
                        'sum(kasko.agent_amount) as agent_kasko_amount',
                    ])
                        ->andWhere('status in (' . implode(',', [Kasko::STATUS['payed'], Kasko::STATUS['attached'], Kasko::STATUS['processed'], Kasko::STATUS['policy_generated']]) . ")")
                        ->andWhere('payed_date between :start_time and ' . time())
                        ->groupBy('f_user_id')
                ],
                '"kasko"."f_user_id" = "agent"."f_user_id"'
            )
            ->leftJoin(
                [
                    "travel" => Travel::find()->select([
                        "count(travel.id) as travel_count",
                        'f_user_id',
                        'sum(travel.agent_amount) as agent_travel_amount',
                    ])
                        ->andWhere('status in (' . implode(',', [Travel::STATUSES['payed'], Travel::STATUSES['waiting_for_policy'], Travel::STATUSES['received_policy']]) . ")")
                        ->groupBy('f_user_id')
                ],
                '"travel"."f_user_id" = "agent"."f_user_id"'
            )
            ->andWhere(['not', ['f_user.status' => User::STATUS_DELETED]]);

        $last_week_monday = strtotime('-2 Monday');
        $beginning_of_month = date_create_from_format('Y-m-d', date('Y-m-01'))->setTime(0, 0, 0)->getTimestamp();

        $query = (new Query)->select([
            'sum(policy_count) as policy_count',
            'sum(policy_amount) as policy_amount',
        ])->from($query);
        $weekly_policy_count = $query->params(['start_time' => $last_week_monday])->createCommand()->queryOne();
        $monthly_policy_count = $query->params(['start_time' => $beginning_of_month])->createCommand()->queryOne();

        return [
            'agent_count' => $agent_count->count(),
            'weekly_policy_count' => (int)$weekly_policy_count['policy_count'],
            "weekly_added_agent_count" => $agent_count->andWhere(['>', 'f_user.created_at', $last_week_monday])->count(),
            'monthly_policy_count' => (int)$monthly_policy_count['policy_count'],
            'monthly_policy_amount' => (int)$monthly_policy_count['policy_amount'],
        ];
    }

    /**
     * @OA\Get(
     *     path="/agent/all",
     *     summary="Method to get all agents with or without pagination",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="filter[search]", in="query", @OA\Schema (type="string"), description="search from first_name, last_name, inn"),
     *     @OA\Parameter (name="filter[status]", in="query", @OA\Schema (type="integer"), description="9 => inactive, 10 => active"),
     *     @OA\Parameter (name="filter[product_id]", in="query", @OA\Schema (type="integer"), description="osago => 1, kasko => 2, travel => 3"),
     *     @OA\Parameter (name="filter[agent_id]", in="query", @OA\Schema (type="integer"), description="id of agent"),
     *     @OA\Parameter (name="page", in="query", @OA\Schema (type="integer|null"), description="page number"),
     *     @OA\Parameter (name="sort", in="query", @OA\Schema (type="string"), example="-created_at", description="order by created_at, inn, policy_count, policy_amount, for desc use '-' sign prefix"),
     *     @OA\Response(
     *         response="200_1", description="if send page param with number",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/agent_for_table")),
     *              @OA\Property (property="pages", type="object", ref="#/components/schemas/pages")
     *        )
     *     ),
     *     @OA\Response(
     *         response="200_2", description="if do not send page",
     *         @OA\JsonContent(
     *              @OA\Property(property="models", type="array",  @OA\Items(type="object", ref="#components/schemas/agent_for_table")),
     *              @OA\Property (property="pages", type="boolean", example=false)
     *        )
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionAll()
    {
        GeneralHelper::checkPermission();

        $searchModel = new AgentSearch();
        $dataProvider = $searchModel->search($this->get['filter'] ?? []);

        return [
            'models' => $dataProvider->getModels(),
            'pages' => $dataProvider->getPagination()
        ];
    }

    /**
     * @OA\Post(
     *     path="/agent/create",
     *     summary="create new agent",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"first_name", "last_name", "contract_number", "logo", "status", "password", "repeat_password", "phone"},
     *                 @OA\Property (property="first_name", type="string", example="Jobir"),
     *                 @OA\Property (property="last_name", type="string", example="Yusupov"),
     *                 @OA\Property (property="contract_number", type="string", example="wert3456"),
     *                 @OA\Property (property="logo", type="file"),
     *                 @OA\Property (property="status", type="integer", example=10, description="9 => inactive, 10 => active"),
     *                 @OA\Property (property="files[]", type="array",  @OA\Items(type="file"), description="eng ko'pi bilan 5 ta fayl yuborish mumkin"),
     *                 @OA\Property (property="phone", type="string", example="998946464400"),
     *                 @OA\Property (property="password", type="string", example="test"),
     *                 @OA\Property (property="repeat_password", type="string", example="test"),
     *                 @OA\Property (property="inn", type="string", example="112234hhjjjj4444"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created agent",
     *          @OA\JsonContent( type="object", ref="#components/schemas/agent")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionCreate()
    {
        GeneralHelper::checkPermission();

        $model = new CreateAgentForm();

        $model->setAttributes($this->post);
        $model->logo = UploadedFile::getInstanceByName('logo');
        $model->files = UploadedFile::getInstancesByName('files');
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Put(
     *     path="/agent/set-agent-product-coeff",
     *     summary="set agent product coeff",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"agent_id", "product_ids", "coeffs"},
     *                 @OA\Property (property="agent_id", type="integer", example=18),
     *                 @OA\Property (property="product_ids", type="array", @OA\Items(type="integer", example=2), description="osago => 1, kasko => 2, travel => 3"),
     *                 @OA\Property (property="coeffs", type="array", @OA\Items(type="float", example=12.5), description="product id ga tegishli koeffsientlar, product id nechinchi turgan bo'lsa unga tegishli koeffsient ham o'sha o'rinda bo'lishi kerak"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="updated agent",
     *          @OA\JsonContent( type="object", ref="#components/schemas/agent")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionSetAgentProductCoeff()
    {
        GeneralHelper::checkPermission();

        $model = new SetAgentProductCoeffForm();

        $model->setAttributes($this->put);
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Post(
     *     path="/agent/update",
     *     summary="update agent",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"agent_id", "first_name", "last_name", "contract_number", "status", "phone"},
     *                 @OA\Property (property="agent_id", type="integer", example=18),
     *                 @OA\Property (property="first_name", type="string", example="Jobir"),
     *                 @OA\Property (property="last_name", type="string", example="Yusupov"),
     *                 @OA\Property (property="contract_number", type="string", example="wert3456"),
     *                 @OA\Property (property="logo", type="file"),
     *                 @OA\Property (property="status", type="integer", example=10, description="9 => inactive, 10 => active"),
     *                 @OA\Property (property="files[]", type="array",  @OA\Items(type="file"), description="eng ko'pi bilan 5 ta fayl yuborish mumkin"),
     *                 @OA\Property (property="phone", type="string", example="998946464400"),
     *                 @OA\Property (property="password", type="string", example="test"),
     *                 @OA\Property (property="repeat_password", type="string", example="test"),
     *                 @OA\Property (property="inn", type="string", example="112234hhjjjj4444"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="created agent",
     *          @OA\JsonContent( type="object", ref="#components/schemas/agent")
     *     ),
     *     @OA\Response(response="422", ref="#/components/responses/error_422"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionUpdate()
    {
        GeneralHelper::checkPermission();

        $model = new UpdateAgentForm();

        $model->setAttributes($this->post);
        $model->logo = UploadedFile::getInstanceByName('logo');
        $model->files = UploadedFile::getInstancesByName('files');
        if ($model->validate())
            return $model->save();

        return $this->sendFailedResponse($model->getErrors(), 422);
    }

    /**
     * @OA\Delete(
     *     path="/agent/delete-file",
     *     summary="delete file of agent by id of file",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (name="agent_file_id", in="query", @OA\Schema (type="integer"), example=34),
     *     @OA\Response(
     *         response="200", description="if successfully deleted API return true",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDeleteFile($agent_file_id)
    {
        GeneralHelper::checkPermission();

        if (!is_null($agent_file_id) and is_numeric($agent_file_id) and (int)$agent_file_id == $agent_file_id and $file = AgentFile::findOne($agent_file_id))
        {
            $root = str_replace('\\', '/', \Yii::getAlias('@backend') . '/web/');
            if (is_file($root .  $file->path))
                unlink($root .  $file->path);
            $file->delete();
            return true;
        }

        throw new BadRequestHttpException(\Yii::t('app', 'ID is incorrect'));
    }

    /**
     * @OA\Get(
     *     path="/agent/get-by-id",
     *     summary="get agent by id",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="agent",
     *         @OA\JsonContent(type="object", ref="#components/schemas/agent_get_by_id")
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/error_404"),
     * )
     */
    public function actionGetById($id)
    {
        GeneralHelper::checkPermission();

        $agent = (new AgentSearch())->search(['agent_id' => $id])->getModels();
        if (empty($agent))
            return null;

        $agent = $agent[0];
        $_agent = Agent::findOne($id);
        return array_merge($agent,
            [
                'logo' => GeneralHelper::env('backend_project_website') . $agent['logo'],
                'agentFiles' => AgentFile::getShortArrCollection($_agent->agentFiles),
                'agentProductCoeffs' => AgentProductCoeff::getShortArrCollection($_agent->agentProductCoeffs)
            ]
        );
    }

    /**
     * @OA\Delete(
     *     path="/agent/delete",
     *     summary="delete agent by id",
     *     tags={"AgentController"},
     *     security={ {"bearerAuth": {} } },
     *     @OA\Parameter (ref="#/components/parameters/id"),
     *     @OA\Response(
     *         response="200", description="if successfully deleted API return true",
     *         @OA\JsonContent(type="boolean", example=true)
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/error_400"),
     *     @OA\Response(response="401", ref="#/components/responses/error_401"),
     * )
     */
    public function actionDelete($id)
    {
        GeneralHelper::checkPermission();

        $agent = $this->findById($id);
        $user = $agent->user;
        $user->status = User::STATUS_DELETED;
        $user->save();

        return true;
    }

    /**
     * @param $id
     * @return array|Surveyer
     * @throws BadRequestHttpException
     */
    public function findById($id)
    {
        if (!is_null($id) and is_numeric($id) and (int)$id == $id
            and $agent = Agent::find()->leftJoin('f_user', '"agent"."f_user_id" = "f_user"."id"')
                ->andWhere(['not', ['f_user.status' => User::STATUS_DELETED]])->andWhere(['agent.id' => $id])->one()
        )
            return $agent;

        throw new BadRequestHttpException("ID is incorrect");
    }
}