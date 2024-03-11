<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AccidentPartnerProgram */

$this->title = Yii::t('app', 'Create Accident Partner Program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accident Partner Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="accident-partner-program-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
