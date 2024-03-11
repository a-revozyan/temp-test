<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Message;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AgeGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Message translates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="translates-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'category',
            'message',
            [
                'attribute' => 'ru',
                'label' => 'RU',
                'value' =>  'ru.translation',
            ],
            [
                'attribute' => 'uz',
                'label' => 'UZ',
                'value' =>  'uz.translation',
            ],
            [
                'attribute' => 'en',
                'label' => 'EN',
                'value' =>  'en.translation',
            ],
            //['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => '{update}',
                'urlCreator' => function ($action, $model) {
                    if ($action === 'update') {
                        return \yii\helpers\Url::to(['site/translate-update', 'id' => $model->id]);
                    }

                }
            ],
        ],
    ]); ?>


</div>
