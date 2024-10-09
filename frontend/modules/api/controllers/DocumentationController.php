<?php

namespace frontend\modules\api\controllers;

use yii\web\Controller;

/**
 * Class DocumentationController
 * @package frontend\modules\api\controllers
 */
class DocumentationController extends Controller
{
    public $layout = 'main';

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}