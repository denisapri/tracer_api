<?php
namespace app\models;
use Yii;
use yii\db\ActiveQuery;
class BaseQuery extends ActiveQuery
{
    function deleted($prefix=NULL,$alias=NULL)
    {
        $this->andWhere(($alias!=NULL ? $alias.'.' : '').( $prefix ? $prefix.'_' : '' ).'deleted_at is not null');
        return $this;
    }
    function notDeleted($prefix=NULL,$alias=NULL)
    {
        $this->andWhere(($alias!=NULL ? $alias.'.' : '').( $prefix ? $prefix.'_' : '' ).'deleted_at is null');
        return $this;
    }
    function active($prefix=NULL,$alias=NULL)
    {
        $this->andWhere([($alias!=NULL ? $alias.'.' : '').( $prefix ? $prefix.'_' : '' )."aktif"=>1]);
        return $this;
    }
    function inActive($prefix=NULL,$alias=NULL)
    {
        $this->andWhere(($alias!=NULL ? $alias.'.' : '').( $prefix ? $prefix.'_' : '' ).'aktif=0');
        return $this;
    }
}