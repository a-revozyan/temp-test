<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Travel Family Koefs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-family-koef-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('< Back', ['/travel-program-period/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Create Travel Family Koef', ['create?partner_id=' . Yii::$app->request->get('partner_id') ?? 0], ['class' => 'btn btn-success']) ?>
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
            'members_count',
            'koef',

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
