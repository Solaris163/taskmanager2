<?php


namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use yii\helpers\Url;

class TaskCest
{
    public function checkTask(AcceptanceTester $I)
    {
        $I->amOnPage(Url::toRoute('/task/index'));

        $I->selectOption('Month','5'); //выбрать из выпадающего списка месяц 5
        $I->click('apply');
        $I->see('задача1');
        $I->click('задача1');

        $I->see('задача1');
        $I->fillField('comment','комментарий для задачи номер 1');
        $I->see('Task name');
        $I->click('change_language');
        $I->see('Название задачи');
    }
}