<?php

namespace frontend\widgets;

use common\models\tables\Tasks;
use yii\base\Widget;

class CardPreview extends Widget
{
    public $model;

    public function run()
    {
        if(is_a($this->model, Tasks::class)){
            return $this->render('card_preview', ['model' => $this->model]);
        }
        throw new \Exception("Модель, переданная в виджет CardPreview имеет класс не Tasks");
    }
}