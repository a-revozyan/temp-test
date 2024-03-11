<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">Netkost</span><span class="logo-lg">Netkost Admin</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <?php if(!array_key_exists('partner', $roles)):?>
                <li><?=Html::a('Change password', ['admin/user/change-password'])?></li>
            <?php endif;?>
                <li>
                    <?php echo Yii::$app->user->isGuest ? Html::a('Login', ['site/login']) 
                    : Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->username . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm();
                ?>
                </li>
            </ul>
        </div>
    </nav>
</header>
