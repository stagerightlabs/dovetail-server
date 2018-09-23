<?php

namespace App;

use App\Model;
use App\Category;
use App\Events\NotebookDeletion;

class Notebook extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleting' => NotebookDeletion::class
    ];

    /**
     * The category assigned to this notebook
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
