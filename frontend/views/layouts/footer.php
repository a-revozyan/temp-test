<?php

use yii\helpers\Html;

?>

<!-- Start Footer Top Area -->
<footer class="footer-top-area pt-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-4">
                <div class="single-widget">
                    <a href="/">
                        <img src="/img/netkost_logo1.png" alt="Image" class="w-50 footer-logo">
                    </a>

                    <p class="footer-text"><?=Yii::t('app','Наш уникальный сервис предоставляет Вам сравнить предложения ведущих страховых компаний Узбекистана и выбрать наиболее подходящий для Вас.')?></p>

                    <div class="social-area">
                        <ul>
                            <li>
                                <a href="#"><i class="bx bxl-facebook"></i></a>
                            </li>
                           <!--  <li>
                                <a href="#"><i class="bx bxl-twitter"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="bx bxl-linkedin"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="bx bxl-youtube"></i></a>
                            </li> -->
                            <li>
                                <a href="#"><i class="bx bxl-instagram"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-8">
                <div class="single-widget contact">
                    <h3><?=Yii::t('app', 'Контакты')?></h3>

                    <ul>
                        <li class="pl-0">
                            <a href="tel:Phone: +998 71 200 07 21">
                                <i class="flaticon-call"></i>
                                <span><?=Yii::t('app', 'Hotline:')?></span> 
                                <?=Yii::t('app', 'Phone')?>:  +998 71 200 07 21
                            </a>
                        </li>
                        
                        <li class="pl-0">
                            <a href="mailto:info@netkost.uz">
                                <i class="flaticon-email"></i>
                                <span><?=Yii::t('app', 'Email:')?></span> 
                                info@netkost.uz
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="single-widget footer-products">
                    <h3><?=Yii::t('app', 'Продукты')?></h3>

                    <ul>
                        <li>
                            <?=Html::a(Yii::t('app', 'ОСАГО'), ['product/osago-calc'])?>
                        </li>
                        <li>
                            <?=Html::a(Yii::t('app', 'КАСКО'), ['product/kasko-calc'])?>
                        </li>
                        <li>
                            <?=Html::a(Yii::t('app', 'Страхование туристов'), ['product/travel-calc'])?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="single-widget footer-subscribe">
                    <h3><?=Yii::t('app','Новости')?></h3>

                    <p class="newsletter-p"><?=Yii::t('app','Введите свою электронную почту и будьте в курсе всех акций и скидок')?></p>

                    <div class="subscribe-wrap">
                        <form class="newsletter-form" data-toggle="validator">
                            <input type="email" class="form-control" placeholder="<?=Yii::t('app', 'Enter Your Email')?>" name="EMAIL" required autocomplete="off">

                            <button class="mybtn subscribe-btn" type="submit">
                                <?=Yii::t('app', 'Подписаться')?>
                            </button>

                            <div id="validator-newsletter" class="form-result"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- End Footer Top Area -->

<!-- Start Footer Bottom Area -->
<footer class="footer-bottom-area footer-bottom-electronics-area">
    <div class="container">
        <div class="copy-right">
            <p>Copyright &copy; <?=date('Y')?></p>
        </div>
    </div>
</footer>
<!-- End Footer Bottom Area -->

<!-- Start Go Top Area -->
<div class="go-top">
    <i class='bx bx-chevrons-up'></i>
    <i class='bx bx-chevrons-up'></i>
</div>
<!-- End Go Top Area --> 
        
