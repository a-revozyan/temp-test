<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Relationship */

$this->title = Yii::t('app', 'Create Relationship');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Relationships'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relationship-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
