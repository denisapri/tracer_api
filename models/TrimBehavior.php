<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\base\Behavior;
class TrimBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate($event)
    {
        $attributes = $this->owner->attributes;
        foreach($attributes as $key => $value) { //For all model attributes
            if(!empty($value)){
                $this->owner->$key = trim($this->owner->$key);
            }
        }
    }
}
