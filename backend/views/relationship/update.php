<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Relationship */

$this->title = Yii::t('app', 'Update Relationship: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Relationships'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="relationship-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
