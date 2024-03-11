<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Citizenship */

$this->title = Yii::t('app', 'Create Citizenship');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Citizenships'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="citizenship-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
