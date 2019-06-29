<?php

use \yii\widgets\ActiveForm;

/** @var \app\models\tables\Files $file */
/** @var \app\models\tables\Tasks $model */

$form = ActiveForm::begin();
echo $form->field($model, 'name')->textInput();
//добавлю div с классом row и добавлю класс col-lg-4 к полям формы, оставлю также класс по-умолчанию form-group
echo "<div class=\"row\">";
echo $form->field($model, 'status_id', ['options' => ['class' => 'form-group col-lg-4']])->dropDownList($statuses_array);
echo $form->field($model, 'responsible_id', ['options' => ['class' => 'form-group col-lg-4']])->dropDownList($users_array);
echo $form->field($model, 'deadline', ['options' => ['class' => 'form-group col-lg-4']])->textInput(['type' => 'date']);
echo "</div>";
echo $form->field($model, 'description')->textarea();
echo \yii\helpers\Html::submitButton(\Yii::t("app", "save"), ['class' => 'btn btn-success']);
ActiveForm::end();

//выведем прикрепленные к задаче комментарии
echo '<br>';
echo "<div style='font-weight: 700'> Комментарии к задаче: </div>";
if ($comments) //проверка, есть ли комментарии к этой задаче
{
    foreach ($comments as $comment) //вывод комментариев
    {
        echo "{$comment['user_name']}: {$comment['content']} <br>";
    }
}else echo "К этой задаче пока нет комментариев";

//создаем форму для отправки нового комментария
echo "<div class=\"upload-comment\">";
echo \yii\helpers\Html::beginForm(['save_comment', 'options' => ['class' => 'row']], 'post');
echo \yii\helpers\Html::input('hidden', 'task_id', $model->id);
echo \yii\helpers\Html::label('Оставить новый коментарий &nbsp;');
echo \yii\helpers\Html::input('text', 'comment');
echo "&nbsp; &nbsp; &nbsp;";
echo \yii\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-success']);
echo \yii\helpers\Html::endForm();
echo "</div>";

//выведем прикрепленные к задаче изображения
echo '<br>';
echo "<div style='font-weight: 700'> Изображения к задаче: </div>";
if ($pictures) //проверка, есть ли изображения к этой задаче
{
    foreach ($pictures as $picture) //вывод комментариев
    {
        echo "<img src=\"/img/small/{$picture["name"]}\" alt=\"изображение\"> &nbsp; ";
    }
}else echo "К этой задаче пока нет изображений ";

//создаем форму для загрузки файла
echo "<div class=\"upload-picture\">";
$form = ActiveForm::begin(['action' => '/upload', 'options' => ['class' => 'row']]); //добавил класс "row"
echo $form->field($file, 'task_id', ['options' => ['class' => 'form-group col-lg-4']])->hiddenInput(['value' => $model->id])->label(\Yii::t("app", "upload_picture"));
//добавлю полям класс "col-lg-8" и "col-lg-3"
echo $form->field($file, 'upload', ['options' => ['class' => 'form-group col-lg-5']])->fileInput()->label(false);
echo \yii\helpers\Html::submitButton(\Yii::t("app", "upload"), ['class' => 'btn btn-success form-group col-lg-2']);
ActiveForm::end();
echo "</div>";