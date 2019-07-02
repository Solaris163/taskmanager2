<?php


namespace frontend\controllers;


use common\models\tables\Files;
use app\VarDump;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    public function actionIndex()
    {

        $model = new Files(); //Создадим модель для сохранения данных файла в базу данных
        $currentUserId = \Yii::$app->user->id; //найдем текущего пользователя для передачи его в модель
        $model->user_id = $currentUserId;

        if($model->load(\Yii::$app->request->post()))
        {
            $model->upload = UploadedFile::getInstance($model, 'upload');//сохраним файл во временном хранилище
            $model->name = $model->upload->name;
            $model->save(); //сохраним модель файла в базу
            $model->saveFile(); //сохраним файл в папке img
            Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
        }
    }
}