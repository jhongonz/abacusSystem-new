<?php

namespace Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'mod_id',
        'mod_menu_key',
        'mod_name',
        'mod_route',
        'mod_icon',
        'mod_search',
        'mod_state',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $touches = ['profiles'];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'mod_search';

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    public function profiles(): BelongsToMany
    {
        $relation = $this->belongsToMany(
            Profile::class,
            'privileges',
            'pri__mod_id',
            'pri__pro_id'
        );

        $relation->withPivot(
            'pri__pro_id',
            'pri__mod_id',
            'created_at',
            'updated_at',
            'deleted_at'
        );

        return $relation;
    }

    public function id(): ?int
    {
        return $this->getAttribute('mod_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('mod_id', $id);
        return $this;
    }

    public function menuKey(): ?string
    {
        return $this->getAttribute('mod_menu_key');
    }

    public function changeMenuKey(string $key): self
    {
        $this->setAttribute('mod_menu_key', $key);
        return $this;
    }

    public function name(): string
    {
        return $this->getAttribute('mod_name');
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('mod_name', $name);
        return $this;
    }

    public function route(): ?string
    {
        return $this->getAttribute('mod_route');
    }

    public function changeRoute(?string $route): self
    {
        $this->setAttribute('mod_route', $route);
        return $this;
    }

    public function icon(): ?string
    {
        return $this->getAttribute('mod_icon');
    }

    public function changeIcon(?string $icon): self
    {
        $this->setAttribute('mod_icon', $icon);
        return $this;
    }

    public function search(): ?string
    {
        return $this->getAttribute('mod_search');
    }

    public function changeSearch(string $search): self
    {
        $this->setAttribute('mod_search', $search);
        return $this;
    }

    public function state(): int
    {
        return $this->getAttribute('mod_state');
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('mod_state', $state);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function createdAt(): DateTime
    {
        $datetime = $this->getAttribute('created_at');
        return $this->getDateTime($datetime);
    }

    public function changeCreatedAt(DateTime $datetime): self
    {
        $this->setAttribute('created_at', $datetime);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function updatedAt(): ?DateTime
    {
        $datetime = $this->getAttribute('updated_at');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeUpdatedAt(DateTime $datetime): self
    {
        $this->setAttribute('updated_at', $datetime);
        return $this;
    }

    /**
     * @throws Exception
     */
    private function getDateTime(?string $datetime = null): DateTime
    {
        return new DateTime($datetime);
    }
}
