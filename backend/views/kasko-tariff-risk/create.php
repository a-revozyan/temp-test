<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoTariffRisk */

$this->title = Yii::t('app', 'Create Kasko Tariff Risk');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kasko Tariff Risks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kasko-tariff-risk-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
