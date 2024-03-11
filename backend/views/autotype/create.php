<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Autotype */

$this->title = Yii::t('app', 'Create Autotype');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Autotypes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="autotype-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
