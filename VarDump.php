<?php


namespace app;

//сделал класс для вывода объектов в читабельном виде
class VarDump
{
    public static function varDump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        exit;
    }

    public static function printR($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        exit;
    }

    //надо еще добавить метод без exit, для вывода данных в цикле
}