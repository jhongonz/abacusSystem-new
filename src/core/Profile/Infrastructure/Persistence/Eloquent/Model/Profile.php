<?php

namespace Core\Profile\Infrastructure\Persistence\Eloquent\Model;

use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'user__pro_id', 'pro_id');
    }

    public function pivotModules(): BelongsToMany
    {
        return $this->belongsToMany(
            Module::class,
            'privileges',
            'pri__pro_id',
            'pri__mod_id',
        )
            ->withPivot(
                'pri__pro_id',
                'pri__mod_id',
                'created_at',
                'updated_at',
                'deleted_at'
            );
    }

    public function modules(): Collection
    {
        return $this->pivotModules()->get();
    }

    public function id(): ?int
    {
        return $this->attributes['pro_id'];
    }

    public function changeId(?int $id): void
    {
        $this->attributes['pro_id'] = $id;
    }

    public function name(): ?string
    {
        return $this->attributes['pro_name'];
    }

    public function changeName(string $name): void
    {
        $this->attributes['pro_name'] = $name;
    }

    public function state(): ?int
    {
        return $this->attributes['pro_state'];
    }

    public function changeState(int $state): void
    {
        $this->attributes['pro_state'] = $state;
    }

    public function search(): ?string
    {
        return $this->attributes['pro_search'];
    }

    public function changeSearch(string $search): void
    {
        $this->attributes['pro_search'] = $search;
    }

    public function description(): ?string
    {
        return $this->attributes['pro_description'];
    }

    public function changeDescription(string $description): void
    {
        $this->attributes['pro_description'] = $description;
    }

    /**
     * @throws Exception
     */
    public function createdAt(): ?DateTime
    {
        return ($this->attributes['created_at']) ? $this->getDateTime($this->attributes['created_at']) : $this->attributes['created_at'];
    }

    public function changeCreatedAt(DateTime $datetime): void
    {
        $this->attributes['created_at'] = $datetime;
    }

    /**
     * @throws Exception
     */
    public function updatedAt(): ?DateTime
    {
        return ($this->attributes['updated_at']) ? $this->getDateTime($this->attributes['updated_at']) : $this->attributes['updated_at'];
    }

    public function changeUpdatedAt(DateTime $datetime): void
    {
        $this->attributes['updated_at'] = $datetime;
    }

    /**
     * @throws Exception
     */
    private function getDateTime(?string $datetime = null): DateTime
    {
        return new DateTime($datetime);
    }
}
