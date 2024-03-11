<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoTariff */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kasko Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="kasko-tariff-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'partner_id',
            'name',
            'amount_kind',
            'amount',
            [
                'attribute'=>'file',
                'label'=>'File',
                'format'=>'raw',
                'value'=>Html::a('Download the File', $model->file ? [$model->file] : '#'),
            ],
            'franchise_ru',
            'franchise_en',
            'franchise_uz',
            'only_first_risk_ru',
            'only_first_risk_uz',
            'only_first_risk_en',
            'is_conditional',
            'min_price',
            'max_price',
        ],
    ]) ?>

</div>
