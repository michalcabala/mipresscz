<?php

namespace MiPressCz\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavMenuLocation extends Model
{
    protected $table = 'fmm_menu_locations';

    protected $fillable = ['handle', 'name'];

    /** @return HasMany<NavMenu, $this> */
    public function menus(): HasMany
    {
        return $this->hasMany(NavMenu::class, 'menu_location_id');
    }
}
