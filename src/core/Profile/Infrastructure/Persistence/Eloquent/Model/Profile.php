<?php

namespace Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory;
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
     * @var array
     */
    protected $attributes = [
        'pro_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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

    protected $touches = ['user','pivotModules','modules'];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'pro_search';

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user__pro_id', 'pro_id');
    }

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
        return $this->getAttribute('pro_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('pro_id', $id);
        return $this;
    }

    public function name(): ?string
    {
        return $this->getAttribute('pro_name');
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('pro_name', $name);
        return $this;
    }

    public function state(): int
    {
        return $this->getAttribute('pro_state');
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('pro_state', $state);
        return $this;
    }

    public function search(): ?string
    {
        return $this->getAttribute('pro_search');
    }

    public function changeSearch(?string $search): self
    {
        $this->setAttribute('pro_search', $search);
        return $this;
    }

    public function description(): ?string
    {
        return $this->getAttribute('pro_description');
    }

    public function changeDescription(string $description): self
    {
        $this->setAttribute('pro_description', $description);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function createdAt(): ?DateTime
    {
        $datetime = $this->getAttribute('created_at');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
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
