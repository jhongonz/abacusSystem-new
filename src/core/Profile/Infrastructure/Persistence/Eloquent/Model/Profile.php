<?php

namespace Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profiles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'pro_id';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'pro_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pro_id',
        'pro_name',
        'pro_description',
        'pro_state',
        'pro_search',
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

    /** @var string[] */
    protected $touches = ['user', 'pivotModules', 'modules'];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'pro_search';

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
     * @return HasMany<User, $this>
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user__pro_id', 'pro_id');
    }

    /**
     * @return BelongsToMany<Module, $this>
     */
    public function pivotModules(): BelongsToMany
    {
        $relation = $this->belongsToMany(
            Module::class,
            'privileges',
            'pri__pro_id',
            'pri__mod_id',
        );

        $relation->withPivot(
            'pri__pro_id',
            'pri__mod_id',
            'pri_permission',
            'created_at',
            'updated_at',
            'deleted_at'
        );

        return $relation;
    }

    public function modules(): Model
    {
        return $this->pivotModules()->getModel();
    }

    public function id(): ?int
    {
        /** @var int|null $id */
        $id = $this->getAttribute('pro_id');

        return $id;
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('pro_id', $id);

        return $this;
    }

    public function name(): ?string
    {
        /** @var string|null $name */
        $name = $this->getAttribute('pro_name');

        return $name;
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('pro_name', $name);

        return $this;
    }

    public function state(): int
    {
        /** @var int $state */
        $state = $this->getAttribute('pro_state');

        return $state;
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('pro_state', $state);

        return $this;
    }

    public function search(): ?string
    {
        /** @var string|null $search */
        $search = $this->getAttribute('pro_search');

        return $search;
    }

    public function changeSearch(?string $search): self
    {
        $this->setAttribute('pro_search', $search);

        return $this;
    }

    public function description(): ?string
    {
        /** @var string|null $description */
        $description = $this->getAttribute('pro_description');

        return $description;
    }

    public function changeDescription(string $description): self
    {
        $this->setAttribute('pro_description', $description);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function createdAt(): ?\DateTime
    {
        /** @var string|null $datetime */
        $datetime = $this->getAttribute('created_at');

        return null !== $datetime ? $this->getDateTime($datetime) : null;
    }

    public function changeCreatedAt(\DateTime $datetime): self
    {
        $this->setAttribute('created_at', $datetime);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function updatedAt(): ?\DateTime
    {
        /** @var string|null $datetime */
        $datetime = $this->getAttribute('updated_at');

        return null !== $datetime ? $this->getDateTime($datetime) : null;
    }

    public function changeUpdatedAt(\DateTime $datetime): self
    {
        $this->setAttribute('updated_at', $datetime);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function deletedAt(): ?\DateTime
    {
        /** @var string|null $datetime */
        $datetime = $this->getAttribute('deleted_at');

        return null !== $datetime ? $this->getDateTime($datetime) : null;
    }

    public function changeDeletedAt(\DateTime $datetime): self
    {
        $this->setAttribute('deleted_at', $datetime);

        return $this;
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(string $datetime = 'now'): \DateTime
    {
        return new \DateTime($datetime);
    }
}
