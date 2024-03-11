<?php

use common\models\Partner;
use yii\helpers\Html;
use common\models\Page;

/* @var $this yii\web\View */

$this->title = 'Sug`urta bozori';

$page = Page::findOne(1);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'ru',
    'content' => $page->description_ru
]);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'uz',
    'content' => $page->description_uz
]);

$this->registerMetaTag([
    'name' => 'description',
    'lang' => 'en',
    'content' => $page->description_en
]);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $page->keywords
]);

$this->registerJs("
    $('.full-news').hide();

    // $('.read-more').click(function() {
    //     if($(this).hasClass('open')) {
    //         $(this).next().slideUp(); 
    //         $(this).removeClass('open');
    //         $(this).find('.text').text('".Yii::t('app', 'Подробнее')."');
    //     } else {
    //         $(this).next().slideDown(); 
    //         $(this).addClass('open');
    //         $(this).find('.text').text('".Yii::t('app', 'Скрыть')."');
    //     }
    // });

    $('#plus-osago').hover(function(){
        $(this).find('img').attr('src', '/img/icons/osago_s.png');
    }, function() {
        $(this).find('img').attr('src', '/img/icons/osago_w.png');
    });

    $('#plus-kasko').hover(function(){
        $(this).find('img').attr('src', '/img/icons/kasko_s.png');
    }, function() {
        $(this).find('img').attr('src', '/img/icons/kasko_w.png');
    });

    $('#plus-travel').hover(function(){
        $(this).find('img').attr('src', '/img/icons/travel_s.png');
    }, function() {
        $(this).find('img').attr('src', '/img/icons/travel_w.png');
    });

", \yii\web\View::POS_END);
?>



        <!-- Start Service Area Two -->
        <div class="service-area-two pt-4 our-products">
            <section class="">
                <div class="container">
                    <div class="section-title">
                       <!--  <span>Наши услуги</span> -->
                        <h2><?= Yii::t('app', 'Виды страховых продуктов')?></h2>
                        <p><?= Yii::t('app', 'Уникальный сервис по сравнению условий страхования в крупнейших страховых компаниях')?></p>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mt-3 plus-box" id="plus-osago">
                            <?= Html::a('<div class="plus-box1">
                                <img class="" src="/img/icons/osago_w.png">
                                <p class="mt-3">'.Yii::t('app', 'ОСАГО').'</p>
                            </div>', ['product/osago-calc'])?>
                            
                            <div class="plus-box2">
                                <p><?=Yii::t('app', 'Необходимый, обязательный минимум страховой защиты для водителей авто')?></p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mt-3 plus-box" id="plus-kasko">
                            <?= Html::a('<div class="plus-box1">
                                <img class="" src="/img/icons/kasko_w.png">
                                <p class="mt-3">'.Yii::t('app', 'КАСКО').'</p>
                            </div>', ['product/kasko-calc'])?>
                            
                            <div class="plus-box2">
                                <p><?=Yii::t('app', 'Комплексная программа страхования для всесторонней защиты автомобиля')?></p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mt-3 plus-box" id="plus-travel">
                            <?= Html::a('<div class="plus-box1">
                                <img class="" src="/img/icons/travel_w.png">
                                <p>'.Yii::t('app', 'Страхование туристов').'</p>
                            </div>', ['product/travel-calc'])?>
                            
                            <div class="plus-box2">
                                <p><?=Yii::t('app', 'Не дайте непредвиденным событиям во время поездки, испортить ваш отпуск')?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- End Service Area -->

        <!-- Start Choose Us Area -->
        <section class="service-area-two pt-100 pb-70">
            <div class="section-title">
                <h2><?=Yii::t('app', 'Наши преимущества')?></h2>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 mt-l-0 mt-3">
                        <div class="single-choose wow fadeInUp" data-wow-delay=".2s">
                            <span class="flaticon-team"></span>
                            <h3><?=Yii::t('app', 'Уникальный сервис')?></h3>
                            <p><?=Yii::t('app','Мы предлагаем сравнение условий страхования крупнейших страховых компаний.
С нами вы получаете услуги самых надежных страховых компаний, а также возможность выбрать лучшее предложение в сфере страхования')?></p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 mt-l-0 mt-3">
                        <div class="single-choose wow fadeInUp" data-wow-delay=".4s">
                            <span class="flaticon-support"></span>
                            <h3><?=Yii::t('app', 'Никаких переплат!')?></h3>
                            <p><?=Yii::t('app','Мы предлагаем полисы по действующим тарифам страховых компаний и даже дешевле, потому что мы единственные на рынке электронного страхования')?></p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-sm-6 mt-l-0 mt-3">
                        <div class="single-choose wow fadeInUp" data-wow-delay=".5s">
                            <span class="flaticon-contract"></span>
                            <h3><?=Yii::t('app', 'Честность превыше всего')?></h3>
                            <p><?=Yii::t('app','Наша цель – показать вам все условия страхования честно и открыто, сообщить о любых возможных ограничениях в условиях страхования, чтобы вы были уверены в своей страховке и понимали, на что можете рассчитывать. Для нас это действительно важно!')?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Choose Us Area -->

        <!-- Start Testimonial Area -->
        <!-- <section class="testimonial-area ptb-100">
            <div class="container">
                <div class="section-title">
                    <h2><?=Yii::t('app', 'Что о нас говорят наши клиенты')?></h2>
                </div>

                <div class="testimonial-wrap-two owl-theme owl-carousel">
                    <div class="single-client">
                        <p>Lorem, ipsum dolor sit amet consectetur quam adipisicing elit. Itaque exercitationem quia modi ipsam veniam obcaecati temporibus rerum quam velit ab eius</p>

                        <ul>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                        </ul>

                        <div class="quotes">
                            <i class="flaticon-left-quotes-sign"></i>
                        </div>
                    </div>

                    <div class="single-client">                        
                        <p>Lorem, ipsum dolor sit amet consectetur quam adipisicing elit. Itaque exercitationem quia modi ipsam veniam obcaecati temporibus rerum quam velit ab eius.</p>

                        <ul>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                        </ul>
                        
                        <div class="quotes">
                            <i class="flaticon-left-quotes-sign"></i>
                        </div>
                    </div>

                    <div class="single-client">
                        <p>Lorem, ipsum dolor sit amet consectetur quam adipisicing elit. Itaque exercitationem quia modi ipsam veniam obcaecati temporibus rerum quam velit ab eius.</p>

                        <ul>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                            <li>
                                <i class="bx bxs-star"></i>
                            </li>
                        </ul>
                        
                        <div class="quotes">
                            <i class="flaticon-left-quotes-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- End Testimonial Area -->

        <!-- Begin Our partners -->                
        <section class="team-area pb-4">
            <div class="section-title">
                <h2><?=Yii::t('app', 'Наши партнеры')?></h2>
            </div>

            <div class="container">
                <div class="partner-wrap owl-theme owl-carousel">
                    <?php 
                     $partners=Partner::find()->all();

                     foreach ($partners as $p) {
                         echo '<div class="single-team">
                        <img src="/uploads/partners/'.$p->image.'" class="img-fluid">
                    </div>';
                     }

                     ?>


                    <!-- <div class="single-team">
                        <img src="img/partners/gross.png" class="img-fluid">
                    </div>
                    <div class="single-team">
                        <img src="img/partners/Uzbekinvest.png" class="img-fluid">
                    </div>
                    <div class="single-team">
                        <img src="img/partners/Kafolat.jpg" class="img-fluid">
                    </div>
                    <div class="single-team">
                        <img src="img/partners/Eurasia.png" class="img-fluid">
                    </div>
                    <div class="single-team">
                        <img src="img/partners/ingo.jpg" class="img-fluid">
                    </div>
                    <div class="single-team">
                        <img src="img/partners/alfa-invest.jpg" class="img-fluid">
                    </div> -->
                </div>
            </div>
        </section>
        <!-- End Our partners -->

        

        <!-- Start About Area -->
        <!-- <section class="about-area ptb-100">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="about-content">
                            <span>About Us</span>
                            <h2>Insurance Always Ready to Protect your Life & Business</h2>

                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis</p> 

                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus aliqua suspendris.</p>

                            <div class="about-list">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="about-single-list list-2">
                                            <i class="flaticon-social-care-1"></i>
                                            <span>We are always Care about Client Satisfy</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="about-single-list">
                                            <i class="flaticon-target"></i>
                                            <span>100+ Community Involvement</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="about.html" class="default-btn">
                                Know Details
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="about-img-3 wow fadeInRight" data-wow-delay=".2s">
                            <img src="img/about-img-3.jpg" alt="Image">
                            <div class="about-img-2">
                                <img src="img/about-img-2.jpg" alt="Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- End About Area -->
        
        <!-- End Business Contact Area -->
        <!-- <section class="business-contact-area pb-100">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 p-0">
                        <div class="business-img" style="overflow: hidden;height: 482px;">
                            <video autoplay loop controls muted class="img-fluid" >
                                <source src="img/test.mp4" type="video/mp4">
                            </video>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 p-0">
                        <div class="business-content">
                            <h3>Save up to 30% when you buy small business insurance in online</h3>
                            
                            <a href="#" class="default-btn active ml-0">
                                Contact Us
                            </a>

                            <p>Call Us Today: <br> +82546-564-234</p>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- End Business Contact Area -->

        <!-- Start Choose Us Area -->
        <!-- <section class="chooses-us-area-two pt-100 pb-70">
            <div class="container">
                <div class="section-title">
                    <span>Choose Us</span>
                    <h2>Why Choose Us</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusd tempor incididunt ut labore et dolore printing.</p>
                </div>

                <div class="choose-us-wrap">
                    <ul>
                        <li>
                            <i class="flaticon-health-care"></i>
                            <h3>Clients Focused</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero consequatur totam maxime, minima.</p>
                        </li>

                        <li>
                            <i class="flaticon-kindness"></i>
                            <h3>Service With Love</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero consequatur totam maxime, minima.</p>
                        </li>

                        <li>
                            <i class="flaticon-support"></i>
                            <h3>24/7 Support</h3>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero consequatur totam maxime, minima.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section> -->
        <!-- End Choose Us Area -->

        <!-- Start Team Area -->
        <!-- <section class="team-area pt-100 pb-70">
            <div class="container">
                <div class="section-title">
                    <span>Team</span>
                    <h2>Our Expert Team</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incidiunt labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
                </div>
                
                <div class="team-wrap owl-theme owl-carousel">
                    <div class="single-team">
                        <div class="image">
                            <img src="img/team/1.jpg" alt="image">

                            <ul class="social">
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-linkedin"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="content">
                            <h3>Denial Vetori</h3>
                            <span>Team Lead</span>
                        </div>
                    </div>
                
                    <div class="single-team">
                        <div class="image">
                            <img src="img/team/2.jpg" alt="image">

                            <ul class="social">
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-linkedin"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="content">
                            <h3>David Jon Korola</h3>
                            <span>Marketer</span>
                        </div>
                    </div>
                
                    <div class="single-team">
                        <div class="image">
                            <img src="img/team/3.jpg" alt="image">

                            <ul class="social">
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-linkedin"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="content">
                            <h3>Merris Polar</h3>
                            <span>CEO</span>
                        </div>
                    </div>

                    <div class="single-team">
                        <div class="image">
                            <img src="img/team/4.jpg" alt="image">

                            <ul class="social">
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-facebook"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-linkedin"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" target="_blank">
                                        <i class="bx bxl-instagram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="content">
                            <h3>Jeck Dew</h3>
                            <span>Founder</span>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- End Team Area -->

        <!-- Start Counter Area -->
   <!--      <section class="counter-area-two pt-100 pb-70 jarallax" data-jarallax='{"speed": 0.3}'>
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-counter">
                            <h2>
                                <span class="odometer" data-count="950">00</span> 
                                <span class="target">+</span>
                            </h2>
                            <p>Completed Project</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-counter">
                            <h2>
                                <span class="odometer" data-count="850">00</span> 
                                <span class="target">+</span>
                            </h2>
                            <p>Winning Awards</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-counter">
                            <h2>
                                <span class="odometer" data-count="550">00</span> 
                                <span class="traget">+</span>
                            </h2>
                            <p>Clients</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-counter">
                            <h2>
                                <span class="odometer" data-count="440">00</span> 
                                <span class="target">+</span>
                            </h2>
                            <p>Countries</p>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- End Counter Area -->
        
        

        <!-- Start Any Contact Area -->
        <section class="any-contact-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <div class="contact-text">
                            <h3><?=Yii::t('app', 'Have you any question? Ask Us anything, we’d love to answer!')?></h3>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="contact-call">
                            <h3>
                                <i class="flaticon-call"></i>
                                <?=Yii::t('app', '+824-456-876-521')?>
                                
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Any Contact Area -->

        <!-- Start Blog Area -->
        <section class="blog-area pt-5 pb-70">
            <div class="container">
                <div class="section-title">
                    <h2><?=Yii::t('app', 'Новости')?></h2>
                </div>

                <div class="row">
                    <?php
                    foreach($news as $n):
                    ?>
                    <div class="col-lg-4 col-md-6 mt-l-0 mt-3">
                        <div class="single-blog wow fadeInUp" data-wow-delay=".2s">
                            <?php
                            if(Yii::$app->language == 'ru'):
                                echo Html::a('<img src="/uploads/cnews/'.$n->image_ru.'" alt="Image">', ['site/news-view', 'id' => $n->id]);
                            elseif(Yii::$app->language == 'uz'):
                                echo Html::a('<img src="/uploads/cnews/'.$n->image_uz.'" alt="Image">', ['site/news-view', 'id' => $n->id]);
                            elseif(Yii::$app->language == 'en'):
                                echo Html::a('<img src="/uploads/cnews/'.$n->image_en.'" alt="Image">', ['site/news-view', 'id' => $n->id]);
                            endif;
                            ?>

                            <div class="blog-content">
                                <ul>
                                    <li>
                                        <?=date('d.m.Y', $n->created_at)?>
                                    </li>
                                </ul>

                                <?php
                                if(Yii::$app->language == 'ru'):
                                    echo Html::a('<h3>'.$n->short_info_ru.'</h3>', ['site/news-view', 'id' => $n->id]);
                                elseif(Yii::$app->language == 'uz'):
                                    echo Html::a('<h3>'.$n->short_info_uz.'</h3>', ['site/news-view', 'id' => $n->id]);
                                elseif(Yii::$app->language == 'en'):
                                    echo Html::a('<h3>'.$n->short_info_en.'</h3>', ['site/news-view', 'id' => $n->id]);
                                endif;

                                echo Html::a('<span class="text">'.Yii::t('app', 'Подробнее').'<i class="bx bx-plus"></i></span>', ['site/news-view', 'id' => $n->id], ['class' => 'read-more']);
                                ?>

                                <div class="full-news mt-3">
                                    <p class="pb-5"><?= Yii::t('app', 'Gross Insurance настоятельно рекомендуем воспользоваться услугами страхования автомобиля. Полисы «ОСАГО», AVTO-VIP, Prestige помогут Вам чувствовать себя еще более уверенно на дорогах, ведь GROSS платит всегда!')?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    endforeach;
                    ?>


                </div>
            </div>
        </section>
        <!-- End Blog Area -->

        