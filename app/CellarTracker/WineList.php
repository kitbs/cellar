<?php

namespace Cellar\CellarTracker;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WineList extends Model
{
    protected $table = 'ct_wine_list';
	
	protected $fillable = ['*'];

    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('Cellar\User');
    }

    public function consumed()
    {
        return $this->hasOne('Cellar\CellarTracker\Consumed', 'i_wine', 'i_wine');
    }

    public function purchase()
    {
        return $this->hasOne('Cellar\CellarTracker\Purchase', 'i_wine', 'i_wine');
    }

    public function privateNotes()
    {
        return $this->hasOne('Cellar\CellarTracker\PrivateNotes', 'i_wine', 'i_wine');
    }

    public function availability()
    {
        return $this->hasOne('Cellar\CellarTracker\Availability', 'i_wine', 'i_wine');
    }

    public function pending()
    {
        return $this->hasOne('Cellar\CellarTracker\Pending', 'i_wine', 'i_wine');
    }

    public function inventory()
    {
        return $this->hasMany('Cellar\CellarTracker\Inventory', 'i_wine', 'i_wine');
    }

    public function notes()
    {
        return $this->hasOne('Cellar\CellarTracker\Notes', 'i_wine', 'i_wine');
    }

    public function proReview()
    {
        return $this->hasOne('Cellar\CellarTracker\ProReview', 'i_wine', 'i_wine');
    }
}
