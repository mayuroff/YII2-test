<?php
namespace app\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\ProfileForm;
use app\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $key = Yii::$app->request->get('key');
        if($key) {
            $user = User::findByAuthKey($key);
            if($user && $user->status == 0) {
                $user->status = 1;
                $user->save();
                if (Yii::$app->getUser()->login($user)) {
                    Yii::$app->session->setFlash('success', "Account activation successful.");
                    return $this->redirect('/?r=site/profile');
                }
            } else {
                Yii::$app->session->setFlash('error', "Account could not be activated.");
                return $this->goHome();
            }
        } else {
            $model = new SignupForm();
            if ($model->load(Yii::$app->request->post())) {
                if ($user = $model->signup()) {
                    $to = $user->email;
                    $from = [Yii::$app->params['adminEmail'] => 'admin'];
                    $subject = 'Activate your account';
                    $body = 'Account activation link ' . Yii::$app->urlManager->hostInfo . '?key=' . $user->auth_key;
                    $this->sendEmail($to, $from, $subject, $body);
                    Yii::$app->session->setFlash('success', "Registration successfull. Check your email for an account activation link.");
                } else {
                    Yii::$app->session->setFlash('error', "Registration failed.");
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->redirect('/?r=site/profile');
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionProfile()
    {

        if(Yii::$app->user->isGuest) {
            return $this->goHome();
        } else {
            $action = Yii::$app->request->get('action');
            $userId = Yii::$app->user->id;
            $user = User::findIdentity($userId);
            if($action && $action == 'delete') {
                Yii::$app->user->logout();
                $user->delete();
                return $this->goHome();
            } else {
                $model = new ProfileForm();
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $post = Yii::$app->request->post('ProfileForm');
                    $user->firstName = $post['firstName'];
                    $user->lastName = $post['lastName'];

                    if(!empty($post['password'])) {
                        $user->setPassword($post['password']);
                    }
                    if ($user->save()) {
                        Yii::$app->session->setFlash('success', 'Profile updated successfully.');
                    } else {
                        Yii::$app->session->setFlash('error', 'There was an error while updating your profile.');
                    }

                    return $this->refresh();
                } else {
                    return $this->render('profile', [
                        'model' => $model,
                    ]);
                }
            }
        }
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function sendEmail($to, $from, $subject, $body)
    {
        return Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom($from)
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }

}
