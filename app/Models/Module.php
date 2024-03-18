<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'mod_id';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'mod_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mod__menu_key',
        'mod_name',
        'mod_route',
        'mod_icon',
        'mod_search',
        'mod_state',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    
    public function profiles()
    {
        return $this->belongsToMany(
            Profile::class,
            'privileges',
            'pri__mod_id',
            'pri__pro_id'
        )
        ->withPivot(
            'pri__pro_id',
            'pri__mod_id',
            'created_at',
            'updated_at',
            'deleted_at'
        );
    }
    
    public function id(): null|int
    {
        return $this->attributes['mod_id'];
    }
    
    public function changeId(?int $id): void
    {
        $this->attributes['mod_id'] = $id;
    }
    
    public function menuKey(): null|string
    {
        return $this->attributes['mod_menu_key'];
    }
    
    public function changeMenuKey(string $key): void
    {
        $this->attributes['mod_menu_key'] = $key;
    }
    
    public function name(): string
    {
        return $this->attributes['mod_name'];
    }
    
    public function changeName(string $name): void
    {
        $this->attributes['mod_name'] = $name;
    }
    
    public function route(): null|string
    {
        return $this->attributes['mod_route'];
    }
    
    public function changeRoute(null|string $route): void
    {
        $this->attributes['mod_route'] = $route;
    }
    
    public function icon(): null|string
    {
        return $this->attributes['mod_icon'];
    }
    
    public function changeIcon(null|string $icon): void
    {
        $this->attributes['mod_icon'] = $icon;
    }
    
    public function search(): ?string
    {
        return $this->attributes['mod_search'];
    }
    
    public function changeSearch(string $search): void
    {
        $this->attributes['mod_search'] = $search;
    }
    
    public function state(): int
    {
        return $this->attributes['mod_state'];
    }
    
    public function changeState(int $state): void
    {
        $this->attributes['mod_state'] = $state;
    }
    
    public function createdAt(): string
    {
        return $this->attributes['created_at'];
    }
    
    public function changeCreatedAt(DateTime $datetime): void
    {
        $this->attributes['created_at'] = $datetime;
    }
    
    public function updatedAt(): null|string
    {
        return $this->attributes['updated_at'];
    }
    
    public function changeUpdatedAt(DateTime $datetime): void
    {
        $this->attributes['updated_at'] = $datetime;
    }
}
