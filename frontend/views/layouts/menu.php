<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<!-- Start Preloader Area -->
<div class="preloader">
    <div class="img-logo">
        <img src="/img/netkost_logo1.png" width="200px">
    </div>
   <!--  <div class="lds-ripple">
        <div></div>
        <div></div>
    </div> -->
</div>
<!-- End Preloader Area -->

<!-- Start Heder Area --> 
<header class="header-area fixed-top">
 <!--    <div class="top-header-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="header-content-left">
                        <p>Welcome to NETKOST!</p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <ul class="header-content-right">
                        <li>
                            <a href="tel:+822456974">
                                <i class="bx bx-phone-call"></i>
                                Call Us For Inquiry: +998905149111
                            </a>
                        </li>

                        <li>
                            <a href="mailto:hello@surety.com">
                                <i class="bx bx-envelope"></i>
                                Email: info@netkost.uz
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Start Navbar Area -->
    <div class="nav-area nev-style-two">
        <div class="navbar-area">
            <!-- Menu For Mobile Device -->
            <div class="mobile-nav">
                <a href="<?=Url::to(Yii::$app->homeUrl)?>" class="logo">
                    <img src="/img/logo_w.png" alt="Logo">
                </a>
            </div>

            <!-- Menu For Desktop Device -->
            <div class="main-nav">
                <nav class="navbar navbar-expand-md navbar-light border-bottom">
                    <div class="container">
                        <a class="navbar-brand" style="width: 10%;" href="<?=Url::to(Yii::$app->homeUrl)?>">
                            <img src="/img/netkost_logo1.png" alt="Logo" width="100%">
                        </a>
                        
                        <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                            <ul class="navbar-nav m-auto" style="margin-left: 10%">
                                <li class="nav-item">
                                    <?= Html::a(Yii::t('app', 'ОСАГО'), ['product/osago-calc'], ['class' => 'nav-link'])?>
                                </li>
                                <li class="nav-item">
                                    <?= Html::a(Yii::t('app', 'КАСКО'), ['product/kasko-calc'], ['class' => 'nav-link'])?>
                                </li>
                                <li class="nav-item">
                                    <?= Html::a(Yii::t('app', 'Туристы'), ['product/travel-calc'], ['class' => 'nav-link'])?>
                                </li>
                                <?php
                                if ($home) :
                                ?>
                                
                                <li class="nav-item services">
                                    <?= Yii::t('app', 'Услуги')?>
                                </li>
                                <li class="nav-item about">
                                    <?= Yii::t('app', 'О нас')?>
                                </li>
                                <?php
                                endif;
                                ?>
                            </ul>
                            
                            <!-- Start Other Option -->
                            <div class="others-option">


                                <!-- <div class="option-item">
                                    <i class="search-btn bx bx-search"></i>
                                    <i class="close-btn bx bx-x"></i>
                                    
                                    <div class="search-overlay search-popup">
                                        <div class='search-box'>
                                            <form class="search-form">
                                                <input class="search-input" name="search" placeholder="Search" type="text">

                                                <button class="search-button" type="submit"><i class="bx bx-search"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="option-item pt-4">
                                    <?= \frontend\widgets\LanguageSwitcher::widget([
                                        'parentTemplate' => '{items}',
                                        'activeItemTemplate' => '<span class="setlang active"><a href="{url}">{label}</a></span>',
                                        'itemTemplate' => '<span class="setlang {class}"><a href="{url}">{label}</a></span>'
                                    ]); ?>
                                </div>

                                <!-- <div class="subscribe">
                                    <a href="#subscribe" class="default-btn">
                                        Быстрый расчет
                                    </a>
                                </div> -->
                            </div>
                            <!-- End Other Option -->
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- End Navbar Area -->
</header>
<!-- End Heder Area -->

<!-- Start Sidebar Modal -->
<!-- <div class="sidebar-modal">  
    <div class="modal right fade" id="myModal2">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            <i class="bx bx-x"></i>
                        </span>
                    </button>

                    <h2 class="modal-title">
                        <a href="index-2.html">
                            <img src="img/netkost_logo1.png" alt="Logo">
                        </a>
                    </h2>
                </div>

                <div class="modal-body">
                    <div class="sidebar-modal-widget">
                        <h3 class="title">About Us</h3>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Commodi, asperiores doloribus eum laboriosam praesentium delectus unde magni aut perspiciatis cumque deserunt dolore voluptate, autem pariatur.</p>
                    </div>

                    <div class="sidebar-modal-widget">
                        <h3 class="title">Additional Links</h3>

                        <ul>
                            <li>
                                <a href="log-in.html">Log In</a>
                            </li>
                            <li>
                                <a href="sign-in.html">Sign In</a>
                            </li>
                            <li>
                                <a href="faq.html">FAQ</a>
                            </li>
                            <li>
                                <a href="#">Log Out</a>
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar-modal-widget">
                        <h3 class="title">Contact Info</h3>

                        <ul class="contact-info">
                            <li>
                                <i class="bx bx-location-plus"></i>
                                Address
                                <span>123, Western Road, Melbourne Australia</span>
                            </li>
                            <li>
                                <i class="bx bx-envelope"></i>
                                Email
                                <span>hello@surety.com</span>
                            </li>
                            <li>
                                <i class="bx bxs-phone-call"></i>
                                Phone
                                <span>+822456974</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="sidebar-modal-widget">
                        <h3 class="title">Connect With Us</h3>

                        <ul class="social-list">
                            <li>
                                <a href="#">
                                    <i class='bx bxl-twitter'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-facebook'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-instagram'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-linkedin'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-youtube'></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- End Sidebar Modal -->