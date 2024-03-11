<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UrlCounter */

$this->title = Yii::t('app', 'Create Url Counter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Url Counters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="url-counter-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
