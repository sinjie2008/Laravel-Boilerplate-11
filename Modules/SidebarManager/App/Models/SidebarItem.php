<?php

namespace Modules\SidebarManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\SidebarManager\Database\factories\SidebarItemFactory;

class SidebarItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'icon',
        'route',
        'parent_id',
        'order',
        'permission_required',
        'module',
        'enabled',
    ];

    /**
     * Define the relationship for the parent menu item.
     */
    public function parent()
    {
        return $this->belongsTo(SidebarItem::class, 'parent_id');
    }

    /**
     * Define the relationship for the children menu items.
     */
    public function children()
    {
        return $this->hasMany(SidebarItem::class, 'parent_id')->orderBy('order');
    }

    // Optional: If using factories
    // protected static function newFactory(): SidebarItemFactory
    // {
    //     //return SidebarItemFactory::new();
    // }
}
