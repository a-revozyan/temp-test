<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoTariffRisk */

$this->title = Yii::t('app', 'Update Kasko Tariff Risk: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kasko Tariff Risks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="kasko-tariff-risk-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
