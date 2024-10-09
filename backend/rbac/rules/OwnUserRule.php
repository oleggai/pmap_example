<?php

namespace backend\rbac\rules;

use yii\rbac\Rule;

class OwnUserRule extends Rule
{
    public $name = 'rule_own_user';

    /**
     * @param int|string $userId
     * @param \yii\rbac\Item $item
     * @param array $params
     * @return bool
     */
    public function execute($userId, $item, $params)
    {
        return isset($params['user']) ? $params['user']->id == $userId : false;
    }
}