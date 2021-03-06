<?php

namespace app\models;

use yii\base\Model;
use app\helpers\InitialUserSettings;

class RegForm extends Model
{
    /**
     * Name of user.
     *
     * @var string
     */
    public $name;

    /**
     * Login to go in to system.
     *
     * @var string
     */
    public $login;

    /**
     * Email.
     *
     * @var string
     */
    public $email;

    /**
     * Password to go in to system.
     *
     * @var string
     */
    public $password;

    /**
     * User model.
     *
     * @var null
     */
    private $_user = null;

    /**
     * Validate rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'login',
                    'email',
                    'password',
                ],
                'filter',
                'filter' => 'trim',
            ],
            [
                [
                    'name',
                    'login',
                    'email',
                    'password',
                ],
                'required',
            ],
            [
                [
                    'name',
                    'login'
                ],
                'string',
                'min' => 2,
                'max' => 255,
            ],
            [
                'password',
                'string',
                'min' => 5,
                'max' => 255,
            ],
            [
                'name',
                'unique',
                'targetClass' => User::class,
                'message' => 'This name already exists.',
            ],
            [
                'login',
                'unique',
                'targetClass' => User::class,
                'message' => 'This login already exists.',
            ],
            [
                'email',
                'email',
            ],
            [
                'email',
                'unique',
                'targetClass' => User::class,
                'message' => 'This email already exists.',
            ],
        ];
    }

    /**
     * Attribute labels.
     *
     * @return array
     */
    public function attributeLabels(){

        return[
            'name' => 'Name',
            'login' => 'Login',
            'email' => 'Email',
            'password' => 'Password'
        ];
    }

    /**
     * Register user.
     *
     * @return bool
     */
    public function reg()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_user = new User();

        $this->_user->name = $this->name;
        $this->_user->login = $this->login;
        $this->_user->email = $this->email;

        $this->_user->setPassword($this->password);

        if (!$this->_user->save()){
            return false;
        }

        InitialUserSettings::run($this->_user);

        return true;
    }

    /**
     * Returns user record.
     *
     * @return null|User
     */
    public function getUser()
    {
        return $this->_user;
    }
}
