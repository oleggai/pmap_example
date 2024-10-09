<?php

namespace backend\models;

use common\models\NotWorkedLink;
use common\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class NotWorkedLinkSearch
 * @package common\models
 */
class NotWorkedLinkSearch extends NotWorkedLink
{
    public $ato_type = null;
    public $ato_name = null;
    public $document_name = null;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ato_name', 'document_name', 'link', 'ato_type'], 'trim']
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        /* @var $currentUser User */
        $currentUser = \Yii::$app->user->identity;
        $ato = $currentUser->ato;
        $userType = $currentUser->getType();

        $query = NotWorkedLink::find()
            ->joinWith(['document.ato'])
            ->distinct();

        if($userType !== User::TYPE_ADMIN) {
            $query->andWhere(['ato.id' => ArrayHelper::getColumn($ato->children()->all(), 'id')]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        // загружаем данные формы поиска и производим валидацию
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}