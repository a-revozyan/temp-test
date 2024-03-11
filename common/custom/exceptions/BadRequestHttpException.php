<?php
namespace common\custom\exceptions;

class BadRequestHttpException extends \yii\web\BadRequestHttpException
{
    public $additionalData;

    public function __construct($message = null, $code = 0, $additionalData = null, $previous = null)
    {
        $this->additionalData = $additionalData;
        parent::__construct($message, $code, $previous);
    }
}