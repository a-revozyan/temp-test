<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AccidentPartnerProgram */

$this->title = Yii::t('app', 'Update Accident Partner Program: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accident Partner Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="accident-partner-program-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
