<?php
use Jenssergers\Date\Date;

trait DatesTranslator
{
    public function getCreatedAttribute($created_at)
    {
        return new Date($created_at);
    }
    public function getupdatedAttribute($updated_at)
    {
        return new Date($updated_at);
    }
    public function getDeletedAttribute($deleted_at)
    {
        return new Date($deleted_at);
    }
}
?>