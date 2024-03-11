<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Autobrand */

$this->title = Yii::t('app', 'Create Autobrand');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Autobrands'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="autobrand-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
