<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = Yii::t('app', 'Новости');
?>
<div class="container mt-2">
  <div class="shadow bg-white p-4 mb-3">
    <h2>
    	<?php
    	if(Yii::$app->language == 'ru'):
            echo $model->title_ru;
        elseif(Yii::$app->language == 'uz'):
            echo $model->title_uz;
        elseif(Yii::$app->language == 'en'):
            echo $model->title_en;
        endif;
        ?>
    </h2>
    <div class="row">
    	<div class="col-md-4">
	    	<?php
	    	if(Yii::$app->language == 'ru'):
	            echo "<img src='/uploads/cnews/" . $model->image_ru . "' class='img-fluid'>";
	        elseif(Yii::$app->language == 'uz'):
	            echo "<img src='/uploads/cnews/" . $model->image_uz . "' class='img-fluid'>";
	        elseif(Yii::$app->language == 'en'):
	            echo "<img src='/uploads/cnews/" . $model->image_en . "' class='img-fluid'>";
	        endif;
	        ?>
	    </div>
	     <div class="col-md-8">
	    	<?php
	    	if(Yii::$app->language == 'ru'):
	            echo $model->body_ru;
	        elseif(Yii::$app->language == 'uz'):
	            echo $model->body_uz;
	        elseif(Yii::$app->language == 'en'):
	            echo $model->body_en;
	        endif;
	        ?>
	    </div>
    </div>
    
   
  </div>
</div>