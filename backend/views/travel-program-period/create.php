<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TravelProgramPeriod */

$this->title = Yii::t('app', 'Create Travel Program Period');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Travel Program Periods'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="travel-program-period-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
