<?php
namespace frontend\controllers;
use common\components\Translate;
use common\models\Banner;
use common\models\Category;
use common\models\Country;
use common\models\Program;
use common\models\Show;
use common\models\Tv;
use frontend\models\Json;
use Yii;
use yii\web\Controller;

/**
 * Tvs controller
 */

class TvsController extends BaseController
{

    function actionTvs($alias)
    {
        $country = Country::findOne(['alias' => $alias]);

        if(empty($country)){
            throw new \yii\web\NotFoundHttpException();
        }

        return $this->render('tvs', [
            'tvs'   => $country->getTvs()->all(),
            'country' => $country
        ]);
    }

    /**
     * @param $alias
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionTv($alias)
    {
        $tv = Tv::findOne(['alias' => $alias]);

        if(empty($tv)){
            throw new \yii\web\NotFoundHttpException();
        }

        $json =  Json::programsJson($tv->id);
        $programs = Program::find()
            ->orderBy(['publishedAt' => SORT_DESC])
            ->joinWith('shows')
            ->where(['shows.tv_id' => $tv->id, 'shows.status' => 1, 'programs.status' => 1])
            ->all();

        $program_categories = Show::find()
            ->joinWith('programs')
            ->where(['shows.tv_id' => $tv->id, 'shows.status' => 1])
            ->orderBy(['programs.publishedAt' => SORT_DESC])
            ->all();

        return $this->render('tv', [
            'tv'   => $tv,
            'programs' => $programs,
            'program_categories' => $program_categories,
            'json' => $json
        ]);
    }

    /**
     * @param $alias
     * @param $alias2
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionProgram($alias, $alias2)
    {

        $program = Program::findOne(['alias' => $alias, 'status' =>1]);
        $show = Show::findOne(['alias' => $alias2, 'status' =>1]);

        if(empty($program) || empty($show)){
            throw new \yii\web\NotFoundHttpException();
        }

        $programs = Program::find()
            ->orderBy(['publishedAt' => SORT_DESC])
            ->joinWith('shows')
            ->where(['shows.alias' => $show->alias, 'programs.status' => 1])
            ->all();

        $json_tvs = Json::tvsJson();
        $countries_tvs = Country::find()->with('tvs')->all();
        $categories = Category::find()->orderBy(['updated' => SORT_DESC])->all();
        $banners = Banner::find()->orderBy(['id' => SORT_DESC])->all();

        return $this->render('program', [
            'program' => $program,
            'show' => $show,
            'programs' => $programs,
            'countries_tvs' => $countries_tvs,
            'tvs'   => $json_tvs,
            'categories' => $categories,
            'banners' => $banners,
        ]);
    }
}