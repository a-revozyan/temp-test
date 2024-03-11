<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\KaskoRisk */

$this->title = Yii::t('app', 'Create Kasko Risk');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Kasko Risks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kasko-risk-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
