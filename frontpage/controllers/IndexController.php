<?php

namespace app\controllers;

use app\models\Test;
use yii\web\Controller;

class IndexController extends Controller{

    public function actionIndex(){
//        echo 'index --';
        $model = new Test();
        $data = $model->find()->one();
        $this->layout = "layout1";
        return $this->renderPartial('index',array("data"=>$data));
    }
}