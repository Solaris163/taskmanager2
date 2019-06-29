<?php


namespace common\components;


use common\models\tables\Tasks;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;

class ChangeLanguage extends Component implements BootstrapInterface
{
    public function bootstrap($app)//метод будет вызван при запуске приложения
    {
        $session = \Yii::$app->session;
        if ($language = $session->get('language'))
        {
            \Yii::$app->language = $language;
        }
    }
}