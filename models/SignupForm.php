<?php
namespace app\models;

use yii\base\Model;
use app\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $firstName;
    public $lastName;
    public $email;
    public $password;
    public $repeatPassword;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['firstName', 'trim'],
            ['firstName', 'required'],
            ['firstName', 'string', 'min' => 2, 'max' => 255],

            ['lastName', 'trim'],
            ['lastName', 'required'],
            ['lastName', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['repeatPassword', 'required'],
            ['password', 'string'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->firstName = $this->firstName;
        $user->lastName = $this->lastName;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        return $user->save() ? $user : null;
    }
}
