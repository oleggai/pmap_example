<?php

namespace frontend\modules\api\controllers;

use frontend\modules\api\ApiUser;
use frontend\modules\api\models\Document;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;


/**
 * Class DocumentController
 * @package frontend\modules\api\controllers
 *
 * Examples: http://pmap/frontend/web/index.php?r=api/document&filter[or][][koatuu]=90&filter[or][][decision_number]=8 (koatuu=90 OR decision_number=8)
 * http://pmap/frontend/web/index.php?r=api/document&filter[uuid][in][]=8dbf6f10-971f-4872-aafc-0237412e6ed0&filter[uuid][in][]=e740b603-f36c-444c-8b2c-cd9ce8605fe8 (uuid in(1, 2, 3...))
 * http://pmap/frontend/web/index.php?r=api/document&filter[updated_at][>]=1998-09-23 (updated_at > 1998-09-23)
 * http://pmap/frontend/web/index.php?r=api/document&access-token=59444c51-1c47-4ae8-b018-da0357362293&filter[document_type][like]=генеральний
 * http://pmap/frontend/web/index.php?r=api/document&&access-token=59444c51-1c47-4ae8-b018-da0357362293&filter[and][][date_approval][>]=1995-01-01&filter[and][][date_approval][<]=2000-01-01&filter[and][][document_type]=Генеральний план
 */
class DocumentController extends ActiveController
{
    public $modelClass = 'frontend\modules\api\models\Document';

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        return $behaviors;
    }


    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();

        // отключить действия "delete" и "create"
        unset($actions['delete'], $actions['create'], $actions['update'], $actions['options'], $actions['view']);

        // настроить подготовку провайдера данных с помощью метода "prepareDataProvider()"
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {
        return (new Document())->search();
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $apiUser = ApiUser::findOne(['access_token' => \Yii::$app->request->get('access-token')]);

        if($apiUser) {
            $apiUser->updateCounters(['count_queries' => 1]);
        }

        return parent::beforeAction($action);
    }
}