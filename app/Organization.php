<?php

namespace App;

use App\User;
use App\Model;
use App\Invitation;
use Illuminate\Support\Collection;

class Organization extends Model
{
    protected $fillable = ['name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'configuration' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**************************************************************************
     * Configuration / Settings
     **************************************************************************/

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
        'label.notebooks' => 'Expermiments',
        'label.sops' => 'S.O.Ps',
        'label.plates' => 'plates'
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
