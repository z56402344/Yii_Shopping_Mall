<?php

namespace app\modules\controllers;

use app\modules\models\Admin;
use yii\web\Controller;

class PublicController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionLogin()
    {
        $model = new Admin();
        $this->layout = false;
        return $this->render('login',['model'=>$model]);
    }
}
