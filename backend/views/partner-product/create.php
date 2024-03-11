<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PartnerProduct */

$this->title = Yii::t('app', 'Create Partner Product');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Partner Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-product-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
