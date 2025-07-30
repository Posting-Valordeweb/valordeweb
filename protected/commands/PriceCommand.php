<?php
Yii::import('application.library.*');

class PriceCommand extends CConsoleCommand
{
    public function actionTo_alexa($google = 0, $bing = 0, $yahoo = 0)
    {
        $serpToAlexa = new SearchStatToAlexa($google, $bing, $yahoo);
        var_dump(number_format($serpToAlexa->convert()));
    }
}