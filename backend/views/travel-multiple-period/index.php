<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Travel Multiple Periods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-multiple-period-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('< Back', ['/travel-program-period/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create Travel Multiple Period', ['create?partner_id=' . Yii::$app->request->get('partner_id') ?? 0], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'partner_id',
                'label' => 'Partner',
                'value' => function($model, $index, $dataColumn) {
                    return $model->partner->name;
                },
            ],
            [
                'attribute' => 'program_id',
                'label' => 'Program',
                'value' => function($model, $index, $dataColumn) {
                    return $model->program->name;
                },
            ],
            'available_interval_days',
            'days',
            //'amount',

            [
                    'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',

                'buttons' => [

                    'update' => function ($url, $model, $key) {

                        return  Html::a('Update', $url . "&partner_id=" . $model->partner->id, ['class' => 'bg-blue label']);

                    },

                ]
            ],
        ],

    ]); ?>


</div>
