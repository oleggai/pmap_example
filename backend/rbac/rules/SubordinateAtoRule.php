<?php

namespace backend\rbac\rules;

use common\models\ato\Ato;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;

class SubordinateAtoRule extends Rule
{
    public $name = 'rule_subordinate_ato';

    /**
     * @param int|string $userId
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($userId, $item, $params)
    {
        /* @var $currentUser User */
        $currentUser = \Yii::$app->user->identity;
        if(!$currentUser->ato) {
            return false;
        }
        /* @var $ato Ato */
        $ato = $params['ato'];

        return $ato->isChildOf($currentUser->ato) || $currentUser->ato->id == $ato->id;
    }
}