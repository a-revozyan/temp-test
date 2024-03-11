<?php

namespace common\helpers;

use common\models\User;
use Dotenv\Dotenv;
use mdm\admin\components\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\ForbiddenHttpException;

class GeneralHelper
{
    public const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function lang_of_local()
    {
        $header = \Yii::$app->request->getHeaders();
        $lang = $header->get('accept-language');
        if (empty($lang) or !in_array($lang, ['ru', 'uz', 'en']))
            $lang = 'ru';

        return $lang;
    }

    public static function generateRandomString($unique = [], $length = 5)
    {
        $charactersLength = strlen(self::CHARACTERS);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::CHARACTERS[rand(0, $charactersLength - 1)];
        }
        if (!empty($unique) and $unique[0]::find()->where([$unique[1] => $randomString])->exists())
            return self::generateRandomString();
        return $randomString;
    }

    public static function export($model_name, $model_search_name,  $col_headers, $values_map = null, $search_params = null)
    {
        $search_params = is_null($search_params) ? \Yii::$app->request->get()['filter'] ?? [] : $search_params;
        $values_map = is_null($values_map) ? array_keys($col_headers) : $values_map;
        $searchModel = new $model_search_name();
        $dataProvider = $searchModel->search($search_params);

        $models = $dataProvider->getModels();
        $models = ArrayHelper::toArray($models, [$model_name => $values_map]);

        $writer = new \XLSXWriter();
        $writer->writeSheet($models, $model_name, $col_headers);

        return self::writeToString($writer, $model_name);
    }

    public static function writeToString($writer, $string)
    {
        $writer->setCompany($string);
        $writer->setTitle($string);
        $writer->setSubject($string);
        $writer->setAuthor($string);
        $writer->setDescription($string);

        return $writer->writeToString();
    }

    public static function checkPermission()
    {
        $roles = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id));
        if (!Helper::checkRoute('/' . Yii::$app->controller->action->getUniqueId()) and !in_array('admin', $roles))  // and !in_array('admin', $roles)
            throw new ForbiddenHttpException();
    }

    public static function env($key)
    {
        $env_path = dirname(dirname(__DIR__));
        $dotenv = Dotenv::createImmutable($env_path);
        $dotenv->safeLoad();

        return $_ENV[$key] ?? null;
    }

    public static function fUser()
    {
        if (
            $authorizationHeader = Yii::$app->request->headers->get('Authorization')
            and preg_match('/^Bearer\s+(.*?)$/', $authorizationHeader, $matches)
            and $bearerToken = $matches[1] ?? false
            and $fuser = User::findIdentityByAccessToken($bearerToken)
        )
            return $fuser;

        return false;
    }

    public static function deleteFolder($folderPath): void
    {
        if (is_dir($folderPath)) {
            $contents = scandir($folderPath);

            foreach ($contents as $item) {
                if ($item != "." && $item != "..") {
                    $itemPath = $folderPath . DIRECTORY_SEPARATOR . $item;

                    if (is_dir($itemPath)) {
                        // Recursively delete subdirectories and files
                        self::deleteFolder($itemPath);
                    } else {
                        // Delete files
                        unlink($itemPath);
                    }
                }
            }

            // Delete the empty directory
            rmdir($folderPath);
        }
    }
}
