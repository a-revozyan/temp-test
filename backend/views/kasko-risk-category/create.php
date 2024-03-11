<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoRiskCategory */

$this->title = Yii::t('app', 'Create Kasko Risk Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kasko Risk Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kasko-risk-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
