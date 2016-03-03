<?php
namespace frontend\controllers;

use common\components\Helper;
use common\components\Search;
use common\models\Banner;
use common\models\Category;
use common\models\Country;
use common\models\Program;
use common\models\Slider;
use common\models\Tv;
use common\models\User;

use frontend\models\Facebook;
use frontend\models\Favorite;
use frontend\models\Json;
use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\bootstrap\Html;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public $successUrl = 'Success';
    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    /**
     * @param $client
     */
    public function successCallback($client)
    {

        $attributes = $client->getUserAttributes();

        if(isset($attributes['email'])) {
            $user = User::find()->where(['email' => $attributes['email']])->one();
            if (!empty($user)) {
                Yii::$app->user->login($user);
            } else {
                // Save session attribute user from FB
                $session = Yii::$app->session;
                $session['attributes'] = $attributes;

                if (!empty($session['attributes'])) {
                    $user = new User();
                    $user->username = str_replace(' ', '_', $session['attributes']['name']);
                    $user->email = $session['attributes']['email'];
                    $user->status = 1;
                    $user->setPassword($session['attributes']['id']);
                    $user->generateAuthKey();
                    if ($user->save()) {
                        $user = User::find()->where(['email' => $session['attributes']['email']])->one();
                        if (!empty($user)) {
                            Yii::$app->user->login($user);
                        }
                    }
                }
            }
        }else{
            Yii::$app->session->setFlash('error', 'Invalid Email.');
        }
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $model = new Tv();
        $json_tvs = Json::tvsJson();
        $countries_tvs = Country::find()->with('tvs')->all();
        $categories = Category::find()->orderBy(['updated' => SORT_DESC])->all();
        $banners = Banner::find()->orderBy(['id' => SORT_DESC])->all();
        $sliders = Slider::find()->orderBy(['id' => SORT_DESC])->all();

        return $this->render('index', [
            'countries_tvs' => $countries_tvs,
            'tvs' => $json_tvs,
            'model' => $model,
            'categories' => $categories,
            'banners' => $banners,
            'sliders' => $sliders,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->redirect(Helper::lang());
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(Helper::lang());
        } else {
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

        return $this->redirect(Helper::lang());
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about', []);
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
            if ($model->signup()) {
                if ($model->sendEmail()) {
                    Yii::$app->session->setFlash('success', 'Activation code sent to written email');
                } else {
                    Yii::$app->session->setFlash('error', 'Sorry, we are unable to activation for email provided.');
                }
            }
            return $this->redirect(Helper::lang('site/login'));
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->redirect(Helper::lang());
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }


        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->redirect(Helper::lang());
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * @param $token
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionActivateAccount($token)
    {
        $model = User::findOne(['activate_token' => $token]);
        if(!$model){
            throw new \yii\web\NotFoundHttpException('404');
        }
        $model->activate_token = NULL;
        $model->status = 1;
        $model->save();

        Yii::$app->session->setFlash('success', 'You successfully activate your account.');

        return $this->redirect(Helper::lang('site/login'));
    }


    /**
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionHistory()
    {
        if (Yii::$app->user->isGuest) {
            throw new \yii\web\NotFoundHttpException();
        }

        $userId = Yii::$app->user->identity->username;
        $histories = Yii::$app->session->get($userId);

        if(!empty($histories)){
            $histories = array_reverse($histories);
        }

        if(Yii::$app->request->post('get') === '') {
            $post = Yii::$app->request->post();
            foreach($post['checkbox'] as $k => $v ){
                foreach($v as $i){
                    unset($histories[$k][$i]);
                }
            }
            Yii::$app->session->set($userId,  array_reverse($histories));
        }

        if(Yii::$app->request->post('all') === ''){
            Yii::$app->cache->delete($userId);
            Yii::$app->session->destroy();
            return $this->redirect(Helper::lang('site/history'));
        }

        return $this->render('history', [
                'histories' => $histories,
            ]
        );
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionFavorites()
    {

        if (Yii::$app->user->isGuest) {
            throw new \yii\web\NotFoundHttpException();
        }

        $tvs = Favorite::find()->where(['user_id' => Yii::$app->user->id])->with('tv')->all();
        return $this->render('favorites', [
            'tvs' => $tvs
        ]);
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionAjaxFavorites()
    {
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\NotFoundHttpException();
        }
        $post = Yii::$app->request->post();

        Helper::favorite($post);
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionWatchlist()
    {
        if (Yii::$app->user->isGuest) {
            throw new \yii\web\NotFoundHttpException();
        }
        return $this->render('watchlist', []);
    }

    /**
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRating()
    {
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\NotFoundHttpException();
        }
        $post = Yii::$app->request->post();
        Helper::rating($post);
    }
}