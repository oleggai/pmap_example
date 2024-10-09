<?php

namespace frontend\modules\api;

/**
 * Class Module
 * @package modules\api
 */
class Module extends \yii\base\Module
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        \Yii::$app->user->identityClass = 'frontend\modules\api\ApiUser';

        \Yii::$app->user->enableSession = false;
    }
}