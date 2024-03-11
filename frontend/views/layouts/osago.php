<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?='<link rel="shortcut icon" type="image/png" href="'.Url::base().'/favicon.png"/>'?>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9MQ2Z22ZD2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-9MQ2Z22ZD2');
    </script>
</head>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(73720195, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/73720195" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<body>

<?php $this->beginBody() ?>


    <?php echo \Yii::$app->view->renderFile(Yii::getAlias('@frontend') . '/views/layouts/menu.php', ['home' => false]); ?>

    <!-- <div class="page-title-area bg-17">
        <div class="container">
            <div class="page-title-content">
                <h2>Car Insurance</h2>
                <ul>
                    <li>
                        <a href="index-2.html">
                            Home 
                        </a>
                    </li>

                    <li>Insurance</li>

                    <li>Car Insurance</li>
                </ul>
            </div>
        </div>
    </div> -->
<div class="main-content">
    <div class="container"><?= Alert::widget() ?></div>
    <?= $content ?>
</div>

    <?php echo \Yii::$app->view->renderFile(Yii::getAlias('@frontend') . '/views/layouts/footer.php'); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
