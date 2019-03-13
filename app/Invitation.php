<?php

namespace App;

use App\User;
use App\UsesHashids;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    use UsesHashids;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'completed_at', 'revoked_at'];

    /**
     * The organization that owns this invitation
     *
     * @return BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Retrieve the invitation code as a string
     *
     * @return string
     */
    public function getCodeAttribute()
    {
        return hashid($this->id, 'invitation');
    }

    /**
     * The user that revoked this invitation
     *
     * @return BelongsTo
     */
    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by')->withTrashed();
    }

    /**
     * Mark this invitation as complete
     *
     * @return bool
     */
    public function complete()
    {
        $this->completed_at = Carbon::now();

        return $this->save();
    }
}
