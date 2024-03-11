<?php

namespace frontend\assets;

use Yii;
use yii\web\AssetBundle;
use yii\helpers\FileHelper;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/site.css',
        'css/owl.theme.default.min.css',
        'css/owl.carousel.min.css',
        'css/magnific-popup.css',
        'css/animate.css',
        'css/boxicons.min.css',
        'css/flaticon.css',
        'css/meanmenu.css',
        'css/nice-select.css',
        'css/odometer.css',
        'css/style.css',
        'css/responsive.css',
        'css/custom.css',
        'css/font-awesome.min.css',
        'css/jquery-ui.css'
    ];
    public $js = [
        'js/popper.min.js',
        'js/jquery.meanmenu.js',
        'js/wow.min.js',
        'js/owl.carousel.js',
        'js/jquery.magnific-popup.min.js',
        'js/jquery.nice-select.min.js',
        'js/parallax.min.js',
        'js/jquery.mixitup.min.js',
        'js/jquery.appear.js',
        'js/odometer.min.js',
        'js/jquery.ajaxchimp.min.js',
        'js/form-validator.min.js',
        'js/contact-form-script.js',
        'js/custom.js',
        'js/jquery-ui.js',
        'js/jquery.ui.touch-punch.min.js',
        'js/moment.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset'
    ];


    public function init()
    {

        $v = '?v=0.0.1';
        $css = [
            'style_css' => '/css/custom.css',
        ];
        $js = [
            'main_js' => '/js/custom.js',
        ];

        foreach ($css as $key => $path) {
            $file_path = Yii::getAlias('@webroot').$path;
            if (file_exists($file_path)) {
                $v = '?v=' . filemtime($file_path);
            }
            $this->css[$key] = $path.$v;
        }
        foreach ($js as $key => $path) {
            $file_path = Yii::getAlias('@webroot').$path;
            if (file_exists($file_path)) {
                $v = '?v=' . filemtime($file_path);
            }
            $this->js[$key] = $path.$v;
        }
        parent::init();
    }
}
