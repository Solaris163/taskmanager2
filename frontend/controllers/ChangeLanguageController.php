<?php


namespace frontend\controllers;


use app\VarDump;
use Yii;
use yii\web\Controller;

class ChangeLanguageController extends Controller
{
    public function actionIndex()
    {
        if (\Yii::$app->language === 'en')
        {
            \Yii::$app->language = 'ru';
        }else \Yii::$app->language = 'en';

        $session = \Yii::$app->session;
        $session->set('language', \Yii::$app->language);
        Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
    }
}