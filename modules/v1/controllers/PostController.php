<?php

namespace app\modules\v1\controllers;

use app\helpers\BehaviorsFromParamsHelper;
use yii\rest\ActiveController;
use app\models\Status;
use app\models\Post;
/*
 *
 * @author Heru Arief Wijaya
 * 2020 @  belajararief.com
 */

class PostController extends ActiveController
{
    public $modelClass = 'app\models\Post';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BehaviorsFromParamsHelper::behaviors($behaviors);
        return $behaviors;
    }

    public function actionShow()
    {
        $post = Post::find()->all();
        return [
            'status' => Status::STATUS_OK,
            'message' => 'Hello :)',
            'data' => 'hy'
        ];
    }
    public function actionCheck($id=0){
        $post = Post::find()->where(['prv_kode'=>$id])->one();
        return [
            'status' => Status::STATUS_OK,
            'message' => 'Hello :)',
            'data' => $post
        ];
    }
    

}