<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelExtraInsurance */

$this->title = Yii::t('app', 'Create Travel Extra Insurance');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Extra Insurances'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-extra-insurance-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
