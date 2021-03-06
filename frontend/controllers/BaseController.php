<?php

namespace frontend\controllers;

use common\models\Ditch;
use Yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BaseController extends Controller
{
    public $ditchKey = 'biquge';

    public function err404($message = '')
    {
        throw new NotFoundHttpException($message);
    }

    public function get($key)
    {
        if ($key && is_string($key)) {
            $content = \Yii::$app->request->get($key);

            return Html::encode($content);
        }

        return null;
    }

    /**
     * 获取指定渠道的小说分类
     * @return mixed
     */
    public function getCategoryList()
    {
        $ditch = Ditch::find()->where(['ditchKey' => $this->ditchKey])->one();
        if (!$ditch) {
            $this->err404('没有找到指定的渠道');
        }
        return $ditch->getAllCategoryByDitch();
    }

    public function beforeAction($action)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $headers = Yii::$app->request->headers;
            $accept = $headers->get('Accept');
            if ('text/html' !== $accept){
                Yii::$app->response->format = Response::FORMAT_JSON;
            }
        }
        if (parent::beforeAction($action)) {
            $this->view->categoryList = $this->getCategoryList();
            return true;
        } else {
            return false;
        }
    }
}
