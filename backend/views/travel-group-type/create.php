<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelGroupType */

$this->title = Yii::t('app', 'Create Travel Group Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Group Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-group-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
