<?php

namespace App;

use App\Team;
use App\User;
use App\Model;
use App\Category;
use App\Notebook;
use App\Invitation;
use Laravel\Cashier\Billable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Organization extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'configuration' => 'array',
    ];

    /**
     * Provide Cashier's billing model methods
     */
    use Billable;

    /**
     * The users that belong to this organization
     *
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The invitations created for this organization
     *
     * @return HasMany
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * The categories that this organization is tracking
     *
     * @return HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * The categories that this organization is tracking
     *
     * @return HasMany
     */
    public function notebooks()
    {
        return $this->hasMany(Notebook::class);
    }

    /**
     * The teams that belong to this organization
     *
     * @return HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * This organization's logo
     *
     * @return MorphOne
     */
    public function logo()
    {
        return $this->morphOne(Logo::class, 'owner')->withDefault([
            'url_original' => ''
        ]);
    }

    /**
     * Return the user's permissions as a collection
     *
     * @return Collection
     */
    public function getSettingsAttribute()
    {
        $configuration = $this->configuration ?? [];

        return collect(array_merge(self::$defaultConfiguration, $configuration));
    }

    /**
     * The default configuration for every organization
     *
     * @var array
     */
    public static $defaultConfiguration = [
        'label.notebooks' => 'Experiments',
        'label.protocols' => 'Protocols',
        'label.plates' => 'Plates'
    ];

    /**
     * Read a configured setting for this organization
     *
     * @param string $setting
     * @return mixed
     */
    public function config($setting)
    {
        return $this->settings->get($setting);
    }

    /**
     * Set the permissions for a user, either one at a time or as a group
     *
     * @param Collection|array|string $setting
     * @param boolean $value
     * @return void
     */
    public function updateConfiguration($setting, $value = false)
    {
        if ($setting instanceof Collection) {
            $setting = $setting->toArray();
        }

        if (!is_array($setting)) {
            $setting = [$setting => $value];
        }

        $existing = $this->configuration ?? [];

        $this->configuration = array_merge($existing, $setting);
    }
}
