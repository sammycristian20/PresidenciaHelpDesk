<?php 

namespace App\Traits;

use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;


/**
 * To register a model's observer when needed (When a model is not used, registering it's observer doesn't make sense)
 *
 * PURPOSE: To allow declaring observer methods in model itself
 * 
 * USAGE : include this trait in the model. Now, create following methods  :
 *  'beforeCreate' for  observer method 'creating'
 *  'afterCreate' for  observer method 'created'
 *  'beforeUpdate' for  observer method 'updating'
 *  'afterUpdate' for  observer method 'updated'
 *  'beforeDeleting' for  observer method 'deleting'
 *  'afterDeleting' for  observer method 'deleted'
 *
 *
 * There are two more methods 'beforeModelActivity' and 'afterModelActivity', which gets called just before any activity
 *  or just after any activity in the model
 */

trait Observable
{
    // trait to include pivot attach and detach events
    use PivotEventTrait;

    protected static function boot()
    {
        parent::boot();


        //different methods for different updates
        static::creating(function($model) {
            if(method_exists($model, 'beforeCreate')){
                $model->beforeCreate($model);
            }

            if(method_exists($model, 'beforeModelActivity')){
                $model->beforeModelActivity($model);
            }

        });

        static::created(function($model) {
            if(method_exists($model, 'afterCreate')){
                $model->afterCreate($model);
            }
            
            if(method_exists($model, 'afterModelActivity')){
                $model->afterModelActivity($model);
            }
        });

        static::updating(function($model) {
            if(method_exists($model, 'beforeUpdate')){
                $model->beforeUpdate($model);
            }

            if(method_exists($model, 'beforeModelActivity')){
                $model->beforeModelActivity($model);
            }

        });

        static::updated(function($model) {
            if(method_exists($model, 'afterUpdate')){
                $model->afterUpdate($model);
            }

            if(method_exists($model, 'afterModelActivity')){
                $model->afterModelActivity($model);
            }
        });

        static::deleting(function($model) {

            if(method_exists($model, 'beforeDelete')){
                $model->beforeDelete($model);
            }

            if(method_exists($model, 'beforeModelActivity')){
                $model->beforeModelActivity($model);
            }
        });

        static::deleted(function($model) {
            if(method_exists($model, 'afterDelete')){
                $model->afterDelete($model);
            }

            if(method_exists($model, 'afterModelActivity')){
                $model->afterModelActivity($model);
            }
        });

        // this event trigger in both save and update case
        static::saved(function($model) {
            if(method_exists($model, 'afterSave')){
                $model->afterSave($model);
            }
        });

        // this event trigger in both save and update case
        static::saving(function($model) {
            if(method_exists($model, 'beforeSave')){
                $model->beforeSave($model);
            }
        });

        // this event triggers attach event during sync or attach 
        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if(method_exists($model, 'afterPivotAttached')){
                $model->afterPivotAttached($model, $relationName, $pivotIds, $pivotIdsAttributes);
            }
        });

        // this event triggers detach during detach or sync
        static::pivotDetached(function ($model, $relationName, $pivotIds) {
            if(method_exists($model, 'afterPivotDetached')){
                $model->afterPivotDetached($model, $relationName, $pivotIds);
            }
        });
    }   
}
