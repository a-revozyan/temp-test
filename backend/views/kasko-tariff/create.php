<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoTariff */

$this->title = Yii::t('app', 'Create Kasko Tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kasko Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kasko-tariff-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
