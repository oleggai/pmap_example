<?php

namespace backend\rbac\rules;

use common\models\ato\Ato;
use common\models\User;
use yii\rbac\Rule;

class SubordinateToRegionRule extends Rule
{
    public $name = 'rule_subordinate_to_region';

    /**
     * User can watch yourself or user which belongs to his region
     * @param int|string $userId
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($userId, $item, $params)
    {
        /* @var $currentUser User */
        $currentUser = \Yii::$app->user->identity;
        /* @var $viewableUser User */
        $viewableUser = $params['user'];
        if($currentUser->id == $viewableUser->id) {
            return true;
        } else {

            if($currentUser->ato && $viewableUser->ato) {

                /* @var $regionOfCurrentUser Ato */
                $regionOfCurrentUser = $currentUser->ato->parents()->andWhere(['type_ato' => Ato::$regionData])->one();
                $regionOfCurrentUser = $regionOfCurrentUser ? : $currentUser->ato;
                /* @var $regionOfViewableUser Ato */
                $regionOfViewableUser = $viewableUser->ato->parents()->andWhere(['type_ato' => Ato::$regionData])->one();
                $regionOfViewableUser = $regionOfViewableUser ? : $viewableUser->ato;

                return $regionOfCurrentUser->id == $regionOfViewableUser->id;
            }
        }
        return false;
    }
}