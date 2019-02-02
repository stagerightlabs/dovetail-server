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
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
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
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Intercept creation to ensure unique slugs
        static::creating(function ($organization) {
            $organization->slug = str_slug($organization->name);

            $existing = DB::table('organizations')
                ->where('slug', 'LIKE', "{$organization->slug}%")
                ->count();

            if ($existing > 0) {
                $organization->slug .= "-{$existing}";
            }
        });
    }

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
        $configuration = collect($this->configuration ?? []);

        return collect(self::$defaultConfiguration)
            ->map(function ($default) use ($configuration) {
                $configured = $configuration->where('key', $default['key'])->first();

                return $configured ?? $default;
            });
    }

    /**
     * The default configuration for every organization
     *
     * @var array
     */
    public static $defaultConfiguration = [
        [
            'key' => 'label.notebooks',
            'value' => 'Experiments',
        ],
        [
            'key' => 'label.protocols',
            'value' => 'Protocols',
        ],
        [
            'key' => 'label.plates',
            'value' => 'Plates',
        ],
    ];

    /**
     * Read a configured setting for this organization
     *
     * @param string $setting
     * @return mixed
     */
    public function config($setting)
    {
        return $this->settings->reduce(function ($carry, $item) use ($setting) {
            if (is_array($item) && $item['key'] == $setting) {
                return $item['value'];
            }
        }, null);
    }

    /**
     * Update an organization setting
     *
     * @param string $setting
     * @param string $value
     * @return void
     */
    public function updateConfiguration($setting, $value)
    {
        // Ensure that we only accept values for known settings
        if (! collect(self::$defaultConfiguration)->pluck('key')->contains($setting)) {
            return;
        }

        // Format the new setting as an array
        $setting = [
            'key' => $setting,
            'value' => $value,
        ];

        $existing = collect($this->configuration ?? []);

        if ($existing->pluck('key')->contains($setting['key'])) {
            $existing = $existing->map(function ($item) use ($setting) {
                if ($item['key'] == $setting['key']) {
                    return $setting;
                }
                return $item;
            });
        } else {
            $existing = $existing->merge([$setting]);
        }

        $this->configuration = $existing->toArray();
    }

    /**
     * Enable activity logging for this model
     */
    use LogsActivity;

    /**
     * The attributes that should be logged by the activity logger
     *
     * @var array
     */
    protected static $logAttributes = ['*'];

    /**
     * Format the automatically logged model event description
     *
     * @param string $eventName
     * @return string
     */
    public function getDescriptionForEvent(string $eventName) : string
    {
        return ucwords($eventName);
    }
}
