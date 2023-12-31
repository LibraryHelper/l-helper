<?php

namespace frontend\controllers;

use common\models\auth\ApiLogin;
use common\models\auth\ConformAccount;
use common\models\auth\RestoreAccount;
use common\models\auth\Signup;
use common\models\user\User;
use common\models\user\UserAuth;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class AuthController extends Controller
{
    public $defaultAction = "login";

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class
        ];
        return $behaviors;
    }

    /**
     * @throws MethodNotAllowedHttpException
     */
    public function actionSignup(Request $request): array|User
    {
        $model = new Signup();
        if ($request->isPost) {
            try {
                $model->load($request->getBodyParams(), '');
                if ($model->validate()) {
                    return $model->signup();
                } else {
                    Yii::$app->response->statusCode = 400;
                    return $model->errors;
                }
            } catch (ErrorException $exception) {
                return ['message' => $exception->getMessage(), 'code' => $exception->getCode()];
            } catch (Exception $e) {
                return ['message' => $e->getMessage(), 'code' => $e->getCode()];
            }
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * @throws MethodNotAllowedHttpException
     * @throws Exception
     */
    public function actionConfirmAccount(): bool|array|UserAuth
    {
        $model = new ConformAccount();
        if (Yii::$app->request->isPost) {
            if ($model->load($this->request->post(), '')) {
                return $model->verifyUser();
            }
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionResendCode(Request $request)
    {
        if ($request->isPost) {
            $data = $request->getBodyParams();
            if (!empty($data['user_token'])) {
                return (new ConformAccount())->resendCode($data['user_token']);
            }
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws MethodNotAllowedHttpException
     */
    public function actionRestoreAccount(): array
    {
        $model = new RestoreAccount();
        if (Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post(), '');
            return $model->restore();
        }
        throw new MethodNotAllowedHttpException();
    }
    
    /**
     * @throws Exception
     * @throws MethodNotAllowedHttpException
     */
    public function actionLogin(): bool|array
    {
        $model = new ApiLogin();
        if (Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post(), '');
            return $model->login();
        }
        throw new MethodNotAllowedHttpException();
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findUser(int $user_id): User
    {
        $user = User::findOne($user_id);
        if ($user instanceof User) {
            return $user;
        }
        throw new NotFoundHttpException();
    }
}