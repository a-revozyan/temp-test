<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TravelMultiplePeriod */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Travel Multiple Periods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="travel-multiple-period-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('< Back', ['/travel-program-period/index'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'partner_id',
            'program_id',
            'available_interval_days',
            'days',
            'amount',
        ],
    ]) ?>

</div>
