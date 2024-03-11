<?php
use mdm\admin\components\MenuHelper;

$menu = array_merge([['label' => 'Menu', 'options' => ['class' => 'header']],
                    // ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    // ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],

                ], MenuHelper::getAssignedMenu(Yii::$app->user->id));



?> 

<aside class="main-sidebar">

    <section class="sidebar">

       
        <?php /* dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Menu', 'options' => ['class' => 'header']],
                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                    ['label' => 'Partners', 'icon' => 'dashboard', 'url' => ['/partner/index']],
                    ['label' => 'Products', 'icon' => 'dashboard', 'url' => ['/product/index']],
                    ['label' => 'Partner products', 'icon' => 'dashboard', 'url' => ['/partner/products']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'ОСАГО',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Виды ТС', 'icon' => 'file-code-o', 'url' => ['autotype/index'],],
                            ['label' => 'Регистрация ТС', 'icon' => 'dashboard', 'url' => ['citizenship/index'],],
                            ['label' => 'Регионы', 'icon' => 'dashboard', 'url' => ['region/index'],],
                            ['label' => 'Периоды страхования', 'icon' => 'dashboard', 'url' => ['period/index'],],
                            ['label' => 'Количество водителей', 'url' => ['number-drivers/index']],
                            ['label' => 'Степень родства', 'url' => ['relationship/index']],
                            ['label' => 'Amounts', 'url' => ['osago-amount/index']],
                        ]
                    ],
                ],
            ]
        )*/ ?>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => $menu,
            ]
        ) ?>

    </section>

</aside>
