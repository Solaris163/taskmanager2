<?php
/**
 * Created by PhpStorm.
 * User: Компьютер
 * Date: 25.05.2019
 * Time: 1:46
 */

namespace frontend\controllers;


//use app\models\filters\TaskFilter;
use common\models\tables\Comments;
use common\models\tables\Files;
use common\models\tables\Tasks;
use common\models\tables\TaskStatuses;
use common\models\User;
//use app\models\TaskList;
//use app\models\TaskCard;
use app\VarDump;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

class TaskController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['card'],
                'rules' => [
                    [
                        'actions' => ['card'], //для экшена actionCard
                        'allow' => true,
                        'roles' => ['@'],// @-значит авторизованные пользователи, вместо @ можно указать роли или разрешения из таблицы "auth_item"
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() //новый индекс, который сделал по образцу как у преподавателя
    {
        $monthArray = ['0' => 'Сбросить фильтр']; //подготовим массив из 12 месяцев для передачи в рендер
        for ($i = 1; $i <= 12; $i++) {
            if ($i < 10)
            {
                $monthArray["0{$i}"] = $i; //прибавляем 0 к ключу месяца, если номер месяца меньше 10
            }else $monthArray["{$i}"] = $i;
        }

        $month = \Yii::$app->request->get()['month']; //получаем из гет запроса месяц для фильтрации задач по месяцу
        if (!$month or $month === '0') //если месяц существует и не равен 0, то передаем в рендер параметр $filter = true
        {
            $filter = false;
        }else $filter = true;

        if ($filter) //если выбран фильтр по месяцу, передаем в dataProvider запрос с выборкой по месяцу
        {
            $dataProvider = new ActiveDataProvider([
                //'query' => Tasks::find()->where(['like', 'deadline', "-{$month}-"])
                'query' => Tasks::find()->andWhere("MONTH(deadline) = {$month}") //на уроке преподователь так сделал
            ]);

            //Кэшируем выборку по месяцу на 30 секунд
            \Yii::$app->db->cache(function () use ($dataProvider) {
                return $dataProvider->prepare();
            }, 30);

        }else $dataProvider = new ActiveDataProvider([ //иначе, передаем запрос без выборки по месяцу
            'query' => Tasks::find()
        ]);

        return $this->render('list_previews',
            [
            'dataProvider' => $dataProvider,
            'monthArray' => $monthArray,
            'filter' => $filter
            ]);
    }

    public function actionCard($id){ //показывает одну карточку задачи, id приходит сам из get-запроса
        $this->layout = 'card-layout';

        $model = Tasks::findOne($id); //находим модель задачи по ее id
        //VarDump::varDump($model);

        //следующее действие наверное следовало бы делать в модели, а не в контроллере
//        $users = User::find()->asArray()->all(); //получаем всех пользователей из базы данных
//        $users_array = []; //соберем ассоциативный массив, где ключом будет id пользователя, а значением его login
//        foreach ($users as $user)
//        {
//            $users_array["{$user["id"]}"] = $user["user_name"]; //затем передадим этот массив в рендер
//        }

        //как делал преподаватель
        $users_array = ArrayHelper::map(
            User::find()->asArray()->all(),
            'id',
            'username'
        );

        //аналогично собираем массив id и name всех статусов задач, для передачи в метод рендер
        $statuses = TaskStatuses::find()->asArray()->all();
        $statuses_array = [];
        foreach ($statuses as $status)
        {
            $statuses_array["{$status["id"]}"] = $status["name"];
        }

        //как делал преподаватель
//        $statuses_array = ArrayHelper::map(
//            TaskStatuses::find()->asArray()->all(),
//            'id',
//            'name'
//        );

        //как еще делал преподаватель
        //$statuses_array = TaskStatuses::find()->select(['name'])->indexBy('id')->column();

        if(Yii::$app->user->can('TaskUpdate')){ //проверка, может ли пользователь изменять задачи
            $canTaskUpdate = true;
        }else $canTaskUpdate = false;

        $request = \Yii::$app->request->post();
        if ($request){ //проверяем, был ли post-запрос на сохранение задачи в базу данных
            if ($canTaskUpdate){
                //если был запрос и пользователь имеет право, то переписываем в свойства модели поля из формы
                $model->load(\Yii::$app->request->post());
                $model->save();
            }else{ //сообщаем пользователю, что у него недостаточно прав для редактирование задачи
                echo "<div style='width: 400px; margin: 0 auto'>";
                echo "У вас недостаточно прав для редактирования задачи <br>";
                echo "<a href=\"\"><button>Вернуться назад</button></a></div>";
                exit;
            }
        }

        $comments = Comments::getComments($model->id); //метод возвращает комментарии для этой задачи
        $pictures = Files::getPictures($model->id);

        $file = new Files(); //создадим модель файла, для передачи в рендер.

        return $this->render('card', [ //отображаем на странице карточку одной задачи
            'model' => $model,
            'users_array' => $users_array,
            //'users_array' => Users::getUsersList(), //как делал преподаватель
            'statuses_array' => $statuses_array,
            'comments' => $comments,
            'file' => $file,
            'pictures' => $pictures
        ]);
    }

    /**
     * экшен сохраняет в базу данных полученный комментарий и перенаправляет обратно на страницу карточки задачи
     */
    public function actionSave_comment()
    {
        $request = \Yii::$app->request->post();
        $currentUserId = \Yii::$app->user->id; //найдем текущего пользователя для передачи его в модель
        $model = new Comments([
            'user_id' => $currentUserId,
            'task_id' => $request["task_id"],
            'content' => $request["comment"]
            ]);
        $model->save();
        Yii::$app->getResponse()->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = Tasks::findOne($id)) !== null) {
            return $model;
        }
    }

}