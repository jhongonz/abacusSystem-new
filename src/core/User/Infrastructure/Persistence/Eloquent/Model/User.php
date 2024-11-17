<?php

namespace Core\User\Infrastructure\Persistence\Eloquent\Model;

use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, int>
     */
    protected $attributes = [
        'user_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_login',
        'password',
        'user__emp_id',
        'user__pro_id',
        'user_state',
        'user_photo',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @var string[]
     */
    protected $touches = ['relationWithEmployee', 'relationWithProfile'];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'user_login';

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
            'email_verified_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    /**
     * @return BelongsTo<Employee, $this>
     */
    public function relationWithEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user__emp_id', 'emp_id');
    }

    public function employee(): Model
    {
        return $this->relationWithEmployee()->getModel();
    }

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function relationWithProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user__pro_id', 'pro_id');
    }

    public function profile(): Model
    {
        return $this->relationWithProfile()->getModel();
    }

    public function id(): ?int
    {
        /** @var int|null $id */
        $id = $this->getAttribute('user_id');

        return $id;
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('user_id', $id);

        return $this;
    }

    public function employeeId(): ?int
    {
        /** @var int|null $employeeId */
        $employeeId = $this->getAttribute('user__emp_id');

        return $employeeId;
    }

    public function changeEmployeeId(int $id): self
    {
        $this->setAttribute('user__emp_id', $id);

        return $this;
    }

    public function profileId(): ?int
    {
        /** @var int|null $profileId */
        $profileId = $this->getAttribute('user__pro_id');

        return $profileId;
    }

    public function changeProfileId(int $id): self
    {
        $this->setAttribute('user__pro_id', $id);

        return $this;
    }

    public function login(): ?string
    {
        /** @var string|null $login */
        $login = $this->getAttribute('user_login');

        return $login;
    }

    public function changeLogin(string $login): self
    {
        $this->setAttribute('user_login', $login);

        return $this;
    }

    public function password(): ?string
    {
        /** @var string|null $password */
        $password = $this->getAttribute('password');

        return $password;
    }

    public function changePassword(string $password): self
    {
        $this->setAttribute('password', $password);

        return $this;
    }

    public function state(): int
    {
        /** @var int $state */
        $state = $this->getAttribute('user_state');

        return $state;
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('user_state', $state);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function createdAt(): ?\DateTime
    {
        /** @var string|null $dateTime */
        $dateTime = $this->getAttribute('created_at');

        return null !== $dateTime ? $this->getDateTime($dateTime) : null;
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

    public function changeUpdatedAt(?\DateTime $datetime): self
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

    public function changeDeletedAt(?\DateTime $datetime): self
    {
        $this->setAttribute('deleted_at', $datetime);

        return $this;
    }

    public function photo(): ?string
    {
        /** @var string|null $photo */
        $photo = $this->getAttribute('user_photo');

        return $photo;
    }

    public function changePhoto(?string $photo): self
    {
        $this->setAttribute('user_photo', $photo);

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
