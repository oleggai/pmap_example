<?php

namespace backend\models;

use common\models\User;
use yii\base\Model;

class ChangePasswordModel extends Model
{
    public $password;
    public $password_new;
    public $password_repeat;

    /**
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        return array_merge($rules, [
            [['password', 'password_new', 'password_repeat'], 'required', 'message' => 'Це поле обов*язкове для заповнення'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password_new', 'message' => 'Новий пароль не співпадає'],
            ['password_new', 'string', 'min' => 6, 'tooShort' => 'Пароль повинен бути не менше 6-ти символів'],
            ['password', 'equalCurrentPassword']
        ]);
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFAULT] = ['password', 'password_new', 'password_repeat'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password_new' => 'Новий пароль',
            'password_repeat' => 'Повторіть ще раз'
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function equalCurrentPassword($attribute, $params)
    {
        /* @var $user User */
        $user = \Yii::$app->user->identity;

        if(!$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Невірний пароль');
        }
    }
}