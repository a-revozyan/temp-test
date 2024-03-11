<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BridgeCompany */

$this->title = 'Create Bridge Company';
$this->params['breadcrumbs'][] = ['label' => 'Bridge Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bridge-company-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
