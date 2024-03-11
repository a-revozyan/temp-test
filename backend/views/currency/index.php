<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\AccidentInsurer;

// $n = new AccidentInsurer();
// $n->accident_id = 9;
// $n->name = "jhbh";
// $n->birthday = date('Y-m-d');
// var_dump($n->validate());
// $n->save();

/* @var $this yii\web\View */
/* @var $searchModel common\models\CurrencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $smessages = \common\models\SourceMessage::find()->where(['like', 'message', '@@%', false])->all();
// foreach($smessages as $s) {
//     $s->message = substr($s->message, 2);
//     $s->message = substr($s->message, 0, -2);
//     $s->save();
// }

// $k = \common\models\Kasko::findOne(25);
// $k->setGrossPolicyNumber();
//var_dump(base64_encode(25));

                  //   Yii::$app->telegram->sendMessage(['chat_id' => 1402313437,'parse_mode' => 'markdown','text' => 'Куплен полис ОСАГО EUROASIA INSURANCE. Номер телефона +998(90)-999-11-91']);
                  //   Yii::$app->telegram->sendMessage(['chat_id' => 123873471,'parse_mode' => 'markdown','text' => 'Куплен полис ОСАГО EUROASIA INSURANCE. Номер телефона +998(90)-999-11-91']);
                  //   Yii::$app->telegram->sendMessage(['chat_id' => 124365663,'parse_mode' => 'markdown','text' => 'Куплен полис ОСАГО EUROASIA INSURANCE. Номер телефона +998(90)-999-11-91']);

                  // Yii::$app->telegram->sendMessage(['chat_id' => 125708395,'parse_mode' => 'markdown','text' => 'Куплен полис ОСАГО EUROASIA INSURANCE. Номер телефона +998(90)-999-11-91']);

$this->title = Yii::t('app', 'Currencies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Currency'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name',
            'code',
            'rate',
            'rate_date',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
