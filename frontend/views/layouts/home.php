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


    <?php echo \Yii::$app->view->renderFile(Yii::getAlias('@frontend') . '/views/layouts/menu.php', ['home' => true]); ?>

    <!-- Start Banner Area -->
    <div class="main-banner-area-two jarallax" data-jarallax='{"speed": 0.3}'>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 pr-3 mt-150 banner-video">
                    <div class="banner-img wow fadeInRight" data-wow-delay=".2s">
                        <video loop autoplay muted class="img-fluid banner-video" >
                            <source class="banner-video-src" src="videos/212.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="banner-text">
                        <!-- <span class="wow fadeInUp" data-wow-delay=".2s">Расчет стоимости страхования в крупных страховых компаниях</span> -->

                        <h1 class="wow fadeInUp banner-heading" data-wow-delay=".4s"><?=Yii::t('app', 'Выберете наилучшую страховку')?></h1>

                        <ul class="nav nav-tabs mynav" id="myTab" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true" style="margin-top: 5px;"><?=Yii::t('app', 'Для меня и семьи')?></a>
                          </li>
                          <li class="nav-item ml-lg-4 ml-0">
                            <a class="nav-link" id="business-tab" href="#business" aria-controls="business" aria-selected="false"><?=Yii::t('app', 'Для бизнеса')?>
                            <?php if(Yii::$app->language == 'ru'): ?>
                              <img class="soon" src="/img/soon_ru.png" width="100px" />
                              <?php elseif(Yii::$app->language == 'uz'): ?>
                              <img class="soon" src="/img/soon_uz.png" width="100px" />
                              <?php elseif(Yii::$app->language == 'en'): ?>
                              <img class="soon" src="/img/soon_en.png" width="100px" />
                            <?php endif; ?>
                            </a>
                          </li>
                        </ul>
                        <div class="tab-content mt-4" id="myTabContent">
                          <div class="tab-pane show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <div class="row">
                                <div class="col product-box text-center osago-box">
                                  <?=Html::a('<img src="/img/icons/osago_s.png" class="product-img" width="60px" style="height: 60px" alt="ОСАГО">', ['product/osago-calc'])?>
                                    <p class="name_product"><?=Yii::t('app', 'ОСАГО')?></p>
                                </div>
                                <div class="col product-box text-center kasko-box">
                                  <?=Html::a('<img src="/img/icons/kasko_s.png" class="product-img" width="50px" style="height: 60px"  alt="KACKО">', ['product/kasko-calc'])?>
                                  <p class="name_product"><?=Yii::t('app', 'КАСКО')?></p>
                                </div>
                                <div class="col product-box text-center travel-box">
                                  <?=Html::a('<img src="/img/icons/travel_s.png" class="product-img" width="55px" style="height: 60px"  alt="Travel">', ['product/travel-calc'])?>
                                  <p class="name_product"><?=Yii::t('app', 'TRAVEL')?></p>
                                </div>
                                <div class="col product-box text-center accident-box">
                                  <?=Html::a('<img src="/img/icons/accident_s.png" class="product-img" width="55px" style="height: 60px"  alt="Accident">', ['product/accident-calc'])?>
                                  <p class="name_product"><?=Yii::t('app', 'Accident')?></p>
                                </div>
                                <div class="col empty-box"></div>
                            </div>
                          </div>
                          <div class="tab-pane" id="business1" role="tabpanel" aria-labelledby="business-tab">
                            <div class="row">
                                <div class="col product-box text-center">
                                  <img src="/img/icons/cargo_s.png" class="product-img" width="60px" height="50px" alt="ОСАГО">
                                </div>
                                <div class="col product-box text-center">
                                  <img src="/img/icons/employer_s.png" class="product-img" width="48px" height="50px" alt="ОСАГО">
                                </div>
                                <div class="col product-box text-center pt-4">
                                  <img src="/img/icons/carrier.png" class="product-img" width="60px" height="50px" alt="ОСАГО">
                                </div>
                                <div class="col product-box text-center">
                                  <img src="/img/icons/factory_s.png" class="product-img" width="50px" height="50px" alt="ОСАГО">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col product-box text-center">
                                  <img src="/img/icons/builder_s.png" class="product-img" width="50px" height="50px" alt="ОСАГО">
                                </div>
                                <div class="col product-box text-center">
                                  <img src="/img/icons/medical_s.png" class="product-img" width="60px" height="50px" alt="ОСАГО">
                                </div>
                                <div class="col product-box text-center">
                                  <img src="/img/icons/corona_s.png" class="product-img" width="48px" height="50px" alt="ОСАГО">
                                </div>
                                <div class="col"></div>
                            </div>
                          </div>
                        </div>

                        <!-- <p class="wow fadeInUp" data-wow-delay=".6s">Рассчитайте стоимость, выберите наилучшее предложение и купите полис онлайн</p>

                        <div class="banner-btn wow fadeInUp" data-wow-delay=".9s">
                            <a href="#" class="default-btn">
                                Сравнить
                            </a>

                        </div> -->
                    </div>  
                </div>

            </div>
        </div>
    </div>

    <?php
    $this->registerJs("


      $('.osago-box').hover(function () {
        $('.main-banner-area-two').fadeOut(300, function () {
          $('.osago-box img').attr('src', 'img/icons/osago_w.png');
          $('.kasko-box img').attr('src', 'img/icons/kasko_w.png');
          $('.travel-box img').attr('src', 'img/icons/travel_w.png');
          $('.accident-box img').attr('src', 'img/icons/accident_w.png');
          $('.house-box img').attr('src', 'img/icons/house_w.png');
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#fff' });

          $('.main-banner-area-two').css({ 'background-color': '#f9a12d' });
          $('h1.banner-heading').css({ 'color': '#fff' });
          $('.name_product').css({ 'color': '#fff' });
          $('.mynav .nav-link').css({ 'color': '#fff' });
          $('.mynav.nav-tabs .nav-link.active').css({ 'border-color' : '#fff' });
          $('.banner-video').attr('src', 'videos/osago.mp4');
          $('.main-banner-area-two').fadeIn(300);
        });

      }, function () {
        $('.main-banner-area-two').fadeOut(100, function () {
          $('.osago-box img').attr('src', 'img/icons/osago_s.png');
          $('.kasko-box img').attr('src', 'img/icons/kasko_s.png');
          $('.travel-box img').attr('src', 'img/icons/travel_s.png');
          $('.accident-box img').attr('src', 'img/icons/accident_s.png');
          $('.house-box img').attr('src', 'img/icons/house_s.png');
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#262566' });

          $('.main-banner-area-two').css({ 'background-color': '#fff' });
          $('h1.banner-heading').css({ 'color': '#262566' });
          $('.name_product').css({ 'color': '#262566' });
          $('.mynav .nav-link').css({ 'color': '#262566' });
          $('.mynav.nav-tabs .nav-link.active').css({ 'border-color' : '#262566' });
          $('.banner-video').attr('src', 'videos/212.mp4');
          $('.main-banner-area-two').fadeIn(300);
        });
      });


      $('.kasko-box').hover(function () {
        $('.main-banner-area-two').fadeOut(100, function () {
          $('.osago-box img').attr('src', 'img/icons/osago_w.png');
          $('.kasko-box img').attr('src', 'img/icons/kasko_w.png');
          $('.travel-box img').attr('src', 'img/icons/travel_w.png');
          $('.accident-box img').attr('src', 'img/icons/accident_w.png');
          $('.house-box img').attr('src', 'img/icons/house_w.png');
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#fff' });

          $('.main-banner-area-two').css({ 'background-color': '#d12c5c' });
          $('h1.banner-heading').css({ 'color': '#fff' });
          $('.name_product').css({ 'color': '#fff' });
          $('.mynav .nav-link').css({ 'color': '#fff' });
          $('.mynav.nav-tabs .nav-link.active').css({ 'border-color' : '#fff' });
          $('.banner-video').attr('src', 'videos/kasko.mp4');
          $('.main-banner-area-two').fadeIn(300);
        });

      }, function () {
        $('.main-banner-area-two').fadeOut(100, function () {
          $('.osago-box img').attr('src', 'img/icons/osago_s.png');
          $('.kasko-box img').attr('src', 'img/icons/kasko_s.png');
          $('.travel-box img').attr('src', 'img/icons/travel_s.png');
          $('.accident-box img').attr('src', 'img/icons/accident_s.png');
          $('.house-box img').attr('src', 'img/icons/house_s.png');
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#262566' });

          $('.main-banner-area-two').css({ 'background-color': '#fff' });
          $('h1.banner-heading').css({ 'color': '#262566' });
          $('.name_product').css({ 'color': '#262566' });
          $('.mynav .nav-link').css({ 'color': '#262566' });
          $('.mynav.nav-tabs .nav-link.active').css({ 'border-color' : '#262566' });
          $('.banner-video').attr('src', 'videos/212.mp4');
          $('.main-banner-area-two').fadeIn(300);
        });
      });


      $('.travel-box').hover(function () {
        $('.main-banner-area-two').fadeOut(100, function () {
          $('.osago-box img').attr('src', 'img/icons/osago_w.png');
          $('.kasko-box img').attr('src', 'img/icons/kasko_w.png');
          $('.travel-box img').attr('src', 'img/icons/travel_w.png');
          $('.accident-box img').attr('src', 'img/icons/accident_w.png');
          $('.house-box img').attr('src', 'img/icons/house_w.png');
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#fff' });

          $('.main-banner-area-two').css({ 'background-color': '#00835e' });
          $('h1.banner-heading').css({ 'color': '#fff' });
          $('.name_product').css({ 'color': '#fff' });
          $('.mynav .nav-link').css({ 'color': '#fff' });
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#fff' });
          $('.mynav.nav-tabs .nav-link.active').css({ 'border-color' : '#fff' });
          $('.banner-video').attr('src', 'videos/travel.mp4');
          $('.main-banner-area-two').fadeIn(300);
        });

      }, function () {
        $('.main-banner-area-two').fadeOut(100, function () {
          $('.osago-box img').attr('src', 'img/icons/osago_s.png');
          $('.kasko-box img').attr('src', 'img/icons/kasko_s.png');
          $('.travel-box img').attr('src', 'img/icons/travel_s.png');
          $('.accident-box img').attr('src', 'img/icons/accident_s.png');
          $('.house-box img').attr('src', 'img/icons/house_s.png');
          $('.nev-style-two .navbar-area .main-nav nav .navbar-nav .nav-item a').css({ 'color': '#262566' });

          $('.main-banner-area-two').css({ 'background-color': '#fff' });
          $('h1.banner-heading').css({ 'color': '#262566' });
          $('.name_product').css({ 'color': '#262566' });
          $('.mynav .nav-link').css({ 'color': '#262566' });
          $('.mynav.nav-tabs .nav-link.active').css({ 'border-color' : '#262566' });
          $('.banner-video').attr('src', 'videos/212.mp4');
          $('.main-banner-area-two').fadeIn(300);
        });
      });


      
    ");
    ?>
    <!-- End Banner Area -->

    <?= $content ?>
    
    <?php echo \Yii::$app->view->renderFile(Yii::getAlias('@frontend') . '/views/layouts/footer.php'); ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
