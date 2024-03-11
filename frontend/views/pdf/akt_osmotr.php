<?php
    function getRoundedLabel($color)
    {
        return <<<HTML
<div style="
                          font-size: 0.1px;
                          width: 8px;
                          height: 8px;
                          background: $color;
                          border-radius: 50%;
                          float: left;
                        "></div>
HTML;

    }
?>
<div id="main">
    <div>
        <div style="float: left; width: 80%; ">
            <h2 style="font-weight: bold;">
                Итоги сканирования
            </h2>
        </div>
        <div style="text-align: right;float: left; width: 20%;">
            <img class="img img-responsive" src="<?= Yii::getAlias('@frontend/web/uploads/partners/') . $partner_logo ?>" />
        </div>
        <div style="clear: both"></div>
    </div>
    <hr>

    <div>
        <div style="float: left; width: 40%; ">
            <p style="margin-top: 30px">
                Сканирование:
                <br>
                <b><?= $car_inspection['uploaded_date'] ?></b>
            </p>
            <p>
                Владелец:
                <br>
                <b><?= $car_inspection['client']['name'] ?></b>
            </p>
            <p>
                Автомобиль:
                <br>
                <b><?= $car_inspection['auto_model']['name'] ?></b>
            </p>
            <p>
                Гос номер:
                <br>
                <b><?= $car_inspection['autonumber'] ?></b>
            </p>
            <p>
                VIN номер:
                <br>
                <b><?= $car_inspection['vin'] ?></b>
            </p>
            <p>
                Пробег:
                <br>
                <b><?= $car_inspection['runway'] ?></b>
            </p>
            <p>
                Год производства ТС:
                <br>
                <b><?= $car_inspection['year'] ?></b>
            </p>
        </div>
        <div style="text-align: right; float: left; width: 60%">
            <!-- Front view -->
            <div id="front" style="width: 100px; height: 74px; margin: auto; position: relative">
                <img style="margin: 0 auto" src="<?= Yii::getAlias('@frontend/web/img/act-inspection/front.png') ?>" alt="Front car" />
                <?php $front_locations = $viewing_angles[\common\models\CarInspection::VIEWING_ANGLE['front']] ?? [] ?>

                <?php $front_window_labels = $front_locations[\common\models\CarInspection::LOCATION['windshield']] ?? [] ?>
                <div style="
                width: 60px;
                height: 15px;
                margin-top: -66px;
                margin-left: 20px;
                opacity: 0.5;
                display: flex;
                flex-wrap: wrap;
              " id="front-window">
                    <?php foreach ($front_window_labels as $front_window_label) : ?>
                        <?= getRoundedLabel($labels[$front_window_label] ?? 'black') ?>
                    <?php endforeach; ?>
                </div>

                <?php $front_bumper_labels = $front_locations[\common\models\CarInspection::LOCATION['front_bumper']] ?? [] ?>
                <div style="
                width: 88px;
                height: 11px;
                margin-top: 30px;
                margin-left: 6px;
                opacity: 0.5;
              " id="front-bumper">
                    <?php foreach ($front_bumper_labels as $front_bumper_label) : ?>
                        <?= getRoundedLabel($labels[$front_bumper_label] ?? 'black') ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Front view end -->

            <div style="width: 300px; margin: 10px auto;">
                <div class="">
                    <!-- Right VIEW -->
                    <div id="right" style="width: 29%; height: 260px; position: relative; float: left;">
                        <img src="<?= Yii::getAlias('@frontend/web/img/act-inspection/right.png') ?>" alt="right car" style="width: 90%" />
                        <?php $right_locations = $viewing_angles[\common\models\CarInspection::VIEWING_ANGLE['right']] ?? [] ?>

                        <?php $right_front_fender_labels = $right_locations[\common\models\CarInspection::LOCATION['right_front_fender']] ?? [] ?>
                        <div id="right-fender" style="
                    margin-top: -230px;
                    margin-left: 35px;
                    width: 40px;
                    height: 55px;
                  ">
                            <?php foreach ($right_front_fender_labels as $right_front_fender_label) : ?>
                                <?= getRoundedLabel($labels[$right_front_fender_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $right_front_door_labels = $right_locations[\common\models\CarInspection::LOCATION['right_front_door']] ?? [] ?>
                        <div id="right-side" style="
                    width: 40px;
                    height: 57px;
                    margin-left: 35px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($right_front_door_labels as $right_front_door_label) : ?>
                                <?= getRoundedLabel($labels[$right_front_door_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $right_rear_door_labels = $right_locations[\common\models\CarInspection::LOCATION['right_rear_door']] ?? [] ?>
                        <div id="right-back-side" style="
                    width: 40px;
                    height: 55px;
                    margin-left: 35px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($right_rear_door_labels as $right_rear_door_label) : ?>
                                <?= getRoundedLabel($labels[$right_rear_door_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $right_back_fender_labels = $right_locations[\common\models\CarInspection::LOCATION['right_rear_fender']] ?? [] ?>
                        <div id="right-back-fender" style="
                    width: 28px;
                    height: 49px;
                    margin-left: 35px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($right_back_fender_labels as $right_back_fender_label) : ?>
                                <?= getRoundedLabel($labels[$right_back_fender_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Right VIEW end -->

                    <!-- TOP VIEW -->
                    <div id="top" style="width: 39%; height: 260px; position: relative; float: left;">
                        <img src="<?= Yii::getAlias('@frontend/web/img/act-inspection/top.png') ?>" alt="mid car" style="width: 90%" />
                        <?php $top_locations = $viewing_angles[\common\models\CarInspection::VIEWING_ANGLE['top']] ?? [] ?>

                        <?php $top_hood_labels = $top_locations[\common\models\CarInspection::LOCATION['Hood']] ?? [] ?>
                        <div id="top-hood" style="
                    width: 90px;
                    height: 46px;
                    margin-top: -235px;
                    margin-left: 19px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($top_hood_labels as $top_hood_label) : ?>
                                <?= getRoundedLabel($labels[$top_hood_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $top_windshield_labels = $top_locations[\common\models\CarInspection::LOCATION['windshield']] ?? [] ?>
                        <div id="top-windshield" style="
                    width: 64px;
                    height: 34px;
                    margin-top: 10px;
                    margin-left: 33px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($top_windshield_labels as $top_windshield_label) : ?>
                                <?= getRoundedLabel($labels[$top_windshield_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $top_roof_labels = $top_locations[\common\models\CarInspection::LOCATION['Roof']] ?? [] ?>
                        <div id="top-roof" style="
                    width: 55px;
                    height: 80px;
                    margin-top: 0;
                    margin-left: 37px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($top_roof_labels as $top_roof_label) : ?>
                                <?= getRoundedLabel($labels[$top_roof_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $back_window_labels = $top_locations[\common\models\CarInspection::LOCATION['rear_glass']] ?? [] ?>
                        <div id="back-window" style="
                    width: 55px;
                    height: 25px;
                    margin-top: 2px;
                    margin-left: 37px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($back_window_labels as $back_window_label) : ?>
                                <?= getRoundedLabel($labels[$back_window_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $back_hood_labels = $top_locations[\common\models\CarInspection::LOCATION['back_hood']] ?? [] ?>
                        <div id="back-hood" style="
                    width: 65px;
                    height: 17px;
                    margin-top: 12px;
                    margin-left: 33px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($back_hood_labels as $back_hood_label) : ?>
                                <?= getRoundedLabel($labels[$back_hood_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- TOP VIEW end -->

                    <!-- LEFT VIEW -->
                    <div id="left" style="width: 29%; height: 260px; position: relative; float: right;">
                        <img src="<?= Yii::getAlias('@frontend/web/img/act-inspection/left.png') ?>" alt="left car" style="width: 90%" />
                        <?php $left_locations = $viewing_angles[\common\models\CarInspection::VIEWING_ANGLE['left']] ?? [] ?>

                        <?php $left_front_fender_labels = $left_locations[\common\models\CarInspection::LOCATION['left_front_fender']] ?? [] ?>
                        <div id="left-fender" style="
                    width: 40px;
                    height: 55px;
                    margin-top: -230px;
                    margin-left: 23px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($left_front_fender_labels as $left_front_fender_label) : ?>
                                <?= getRoundedLabel($labels[$left_front_fender_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $left_front_door_labels = $left_locations[\common\models\CarInspection::LOCATION['left_front_door']] ?? [] ?>
                        <div id="left-side" style="
                    width: 40px;
                    height: 55px;
                    margin-left: 23px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($left_front_door_labels as $left_front_door_label) : ?>
                                <?= getRoundedLabel($labels[$left_front_door_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $left_back_door_labels = $left_locations[\common\models\CarInspection::LOCATION['left_rear_door']] ?? [] ?>
                        <div id="left-back-side" style="
                    width: 40px;
                    height: 55px;
                    margin-left: 23px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($left_back_door_labels as $left_back_door_label) : ?>
                                <?= getRoundedLabel($labels[$left_back_door_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>

                        <?php $left_back_fender_labels = $left_locations[\common\models\CarInspection::LOCATION['left_rear_fender']] ?? [] ?>
                        <div id="left-back-fender" style="
                    width: 28px;
                    height: 50px;
                    margin-left: 35px;
                    opacity: 0.5;
                  ">
                            <?php foreach ($left_back_fender_labels as $left_back_fender_label) : ?>
                                <?= getRoundedLabel($labels[$left_back_fender_label] ?? 'black') ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- LEFT VIEW end -->
                </div>
            </div>

            <!-- Back view -->
            <div class="flex-center">
                <div style="
                width: 100px;
                height: 74px;
                margin: auto;
                position: relative;
              ">
                    <img src="<?= Yii::getAlias('@frontend/web/img/act-inspection/back.png') ?>" alt="bottom car" />
                    <?php $back_locations = $viewing_angles[\common\models\CarInspection::VIEWING_ANGLE['behind']] ?? [] ?>

                    <?php $back_window_labels = $back_locations[\common\models\CarInspection::LOCATION['rear_glass']] ?? [] ?>
                    <div id="back-window" style="
                  width: 60px;
                  height: 15px;
                  margin-top: -66px;
                  margin-left: 20px;
                  opacity: 0.5;
                ">
                        <?php foreach ($back_window_labels as $back_window_label) : ?>
                            <?= getRoundedLabel($labels[$back_window_label]) ?>
                        <?php endforeach; ?>
                    </div>

                    <?php $rear_bumper_labels = $back_locations[\common\models\CarInspection::LOCATION['rear_bumper']] ?? [] ?>
                    <div id="back-bumper" style="
                  width: 90px;
                  height: 18px;
                  margin-top: 20px;
                  margin-left: 6px;
                  opacity: 0.5;
                ">
                        <?php foreach ($rear_bumper_labels as $rear_bumper_label) : ?>
                            <?= getRoundedLabel($labels[$rear_bumper_label]) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Back view end -->
        </div>

        <div style="clear: both;"></div>
    </div>

    <div>
        <h3>Описание повреждений:</h3>

        <ol start="1" class="">
            <?php foreach ([0, 4, 1, 5, 2, 6, 3, 7] as $i) : ?>
                <?php $color = array_key_exists($i, $label_objs) ? $label_objs[$i]->color : "black" ?>
                <li>
                    <div class="item-wrapper">
                        <div style="
                                border: 1px solid <?= $color ?>;
                                background: <?= $color ?>;
                                " class="item-num">
                            <?= $i + 1 ?>
                        </div>
                        <p class="item-text"><?= array_key_exists($i, $label_objs) ? $label_objs[$i]->name : "" ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
            <!-- <li>
              <div class="item-wrapper">
                <div class="item-num">10</div>
                <p class="item-text">Трещна на кузове</p>
              </div>
            </li> -->
        </ol>
        <?php $output = new \Mpdf\QrCode\Output\Svg();
        $qrCode = new \Mpdf\QrCode\QrCode(\common\helpers\GeneralHelper::env('front_website_url').'/car-inspection?uuid=' . $car_inspection['uuid']); ?>
        <div style="width: 200px;float: left">
            <figure>
                <div style="margin: 0 10px; text-align: center;"><?= str_replace('<?xml version="1.0"?>', '', $output->output($qrCode, 250, 'white', 'black')); ?></div>
                <figcaption style="text-align: center;font-size:12px;"></figcaption>
            </figure>
        </div>
    </div>

    <div style="page-break-before: always; text-align: center;">
        <?php if (!empty($car_inspection['longitude']) && !empty($car_inspection['latitude'])) : ?>
            <?php $ll = $car_inspection['longitude'] . "," . $car_inspection['latitude'] ?>
            <h3>Место осмотра:</h3>
            <img src="https://static-maps.yandex.ru/1.x/?ll=<?= $ll ?>&lang=ru&size=650,450&z=13&l=map&pt=<?= $ll ?>,pm2rdm" alt="map" style="max-width: 100%; margin: 0 auto;">
        <?php endif; ?>
    </div>

    <div>
        <?php foreach ($image_paths as $image_path) : ?>
            <img src="<?= $image_path ?>" style="width: 100%; margin: 10px;">
        <?php endforeach; ?>
    </div>
</div>
