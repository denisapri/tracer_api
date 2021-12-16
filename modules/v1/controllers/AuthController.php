<?php

namespace app\modules\v1\controllers;

use app\helpers\BehaviorsFromParamsHelper;
use yii\rest\ActiveController;
use app\models\Status;
use app\models\User;
use yii\data\Pagination;
use Yii;
/*
 *
 * @author Heru Arief Wijaya
 * 2020 @  belajararief.com
 */

class AuthController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = BehaviorsFromParamsHelper::behaviors($behaviors);
        return $behaviors;
    }
    public function actionToken()
    {
   
        return [
            'status' => Status::STATUS_OK,
            'message' => 'Hello :)',
            'data' => 'hy'
        ];
    }
   

}