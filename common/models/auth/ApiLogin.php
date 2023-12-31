<?php

namespace common\models\auth;

use common\models\user\User;
use common\models\user\UserAuth;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Login form
 */
class ApiLogin extends Model
{
    public $username;
    public $password;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @param string $attribute the attribute currently being validated
     * @param array|null $params the additional name-value pairs given in the rule
     */
    public function validatePassword(string $attribute, array|null $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('errorMessage', 'Foydalanuvchi nomi yoki parol noto\'g\'ri!');
            }
        }
    }

    /**
     * @return array|bool
     * @throws Exception
     */
    public function login(): bool|array
    {
        if ($this->validate()) {
            if ($this->_user) {
                $auth = UserAuth::findOne(['user_id' => $this->_user->id]);
                if ($this->_user->username === 'admin'){
                    $auth = new UserAuth();
                    $auth->user_id = $this->_user->id;
                }
                $auth->token = Yii::$app->security->generateRandomString(128);
                $auth->token_expiration_date = time() + 3600 * 24 * 30;
                if (!$auth->save()){
                    return $auth->errors;
                }
                return [
                    'token' => $auth->token,
                    'user' => $this->_user
                ];
            }
        }
        Yii::$app->response->statusCode = 422;
        return $this->errors;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
