<?php

namespace Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
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
     * @var array<string, int>
     */
    protected $attributes = [
        'mod_state' => 1,
        'mod_position' => 1,
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
        'mod_position',
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

    /**
     * @var string[]
     */
    protected $touches = ['profiles'];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'mod_search';

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    /**
     * @return BelongsToMany<Profile, $this>
     */
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
        /** @var int|null $id */
        $id = $this->getAttribute('mod_id');

        return $id;
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('mod_id', $id);
        return $this;
    }

    public function menuKey(): ?string
    {
        /** @var string|null $menuKey */
        $menuKey = $this->getAttribute('mod_menu_key');

        return $menuKey;
    }

    public function changeMenuKey(string $key): self
    {
        $this->setAttribute('mod_menu_key', $key);
        return $this;
    }

    public function name(): string
    {
        /** @var string $name */
        $name = $this->getAttribute('mod_name');

        return $name;
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('mod_name', $name);
        return $this;
    }

    public function route(): ?string
    {
        /** @var string|null $route */
        $route = $this->getAttribute('mod_route');

        return $route;
    }

    public function changeRoute(?string $route): self
    {
        $this->setAttribute('mod_route', $route);
        return $this;
    }

    public function icon(): ?string
    {
        /** @var string|null $icon */
        $icon = $this->getAttribute('mod_icon');

        return $icon;
    }

    public function changeIcon(?string $icon): self
    {
        $this->setAttribute('mod_icon', $icon);
        return $this;
    }

    public function search(): ?string
    {
        /** @var string|null $search */
        $search = $this->getAttribute('mod_search');

        return $search;
    }

    public function changeSearch(?string $search): self
    {
        $this->setAttribute('mod_search', $search);
        return $this;
    }

    public function state(): int
    {
        /** @var int $state */
        $state = $this->getAttribute('mod_state');

        return $state;
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
        /** @var string $datetime */
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
        /** @var string|null $datetime */
        $datetime = $this->getAttribute('updated_at');

        return null !== $datetime ? $this->getDateTime($datetime) : null;
    }

    public function changeUpdatedAt(DateTime $datetime): self
    {
        $this->setAttribute('updated_at', $datetime);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function deletedAt(): ?DateTime
    {
        /** @var string|null $datetime */
        $datetime = $this->getAttribute('deleted_at');

        return null !== $datetime ? $this->getDateTime($datetime) : null;
    }

    public function changeDeletedAt(DateTime $datetime): self
    {
        $this->setAttribute('deleted_at', $datetime);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function position(): int
    {
        /** @var int $position */
        $position = $this->getAttribute('mod_position');

        return $position;
    }

    public function changePosition(int $position): self
    {
        $this->setAttribute('mod_position', $position);
        return $this;
    }

    /**
     * @throws Exception
     */
    private function getDateTime(string $datetime = 'now'): DateTime
    {
        return new DateTime($datetime);
    }
}
