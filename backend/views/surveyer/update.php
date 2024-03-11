<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Surveyer */

$this->title = 'Update Surveyer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Surveyers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="surveyer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
