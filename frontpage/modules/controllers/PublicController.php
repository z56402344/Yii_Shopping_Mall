<?php

namespace app\modules\controllers;

use yii\web\Controller;

class PublicController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = false;
        return $this->render('login');
    }
}
