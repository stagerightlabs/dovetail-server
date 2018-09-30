<?php

namespace App;

use App\Page;
use App\Team;
use App\User;
use App\Model;
use App\Category;
use App\Followable;
use App\Organization;
use App\Events\NotebookCreated;
use App\Events\NotebookDeletion;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notebook extends Model
{
    use Followable;

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
        'created' => NotebookCreated::class,
        'deleting' => NotebookDeletion::class,
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

    /**
     * The pages that make up this notebook
     *
     * @return HasMany
     */
    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    /**
     * The user that owns this notebook, if applicable
     *
     * @return BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The team that owns this notebook, if applicable
     *
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * The organization that owns this notebook, if applicable
     *
     * @return BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
