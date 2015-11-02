<?php

namespace Cellar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CTPrivateNotes extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo('Cellar\User');
    }

    public function availability()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function consumed()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function inventory()
    {
        return $this->hasMany('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function list()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function notes()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function pending()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function proReview()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }

    public function purchase()
    {
        return $this->hasOne('Cellar\CTPrivateNotes', 'i_wine', 'i_wine');
    }
}
