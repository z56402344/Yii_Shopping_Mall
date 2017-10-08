<?php

namespace app\controllers;

use yii\web\Controller;

class ProductController extends Controller{

//    $this->layout ->false;
    public function actionIndex(){
        return $this->render('index');
    }

    public function actionDetail(){
        return $this->render('detail');
    }
}