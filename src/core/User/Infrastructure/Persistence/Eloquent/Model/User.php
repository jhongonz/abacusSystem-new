<?php

namespace Core\User\Infrastructure\Persistence\Eloquent\Model;

use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile;
use DateTime;
use Exception;
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
        return $this->getAttribute('user_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('user_id', $id);

        return $this;
    }

    public function employeeId(): ?int
    {
        return $this->getAttribute('user__emp_id');
    }

    public function changeEmployeeId(int $id): self
    {
        $this->setAttribute('user__emp_id', $id);

        return $this;
    }

    public function profileId(): ?int
    {
        return $this->getAttribute('user__pro_id');
    }

    public function changeProfileId(int $id): self
    {
        $this->setAttribute('user__pro_id', $id);

        return $this;
    }

    public function login(): ?string
    {
        return $this->getAttribute('user_login');
    }

    public function changeLogin(string $login): self
    {
        $this->setAttribute('user_login', $login);

        return $this;
    }

    public function password(): ?string
    {
        return $this->getAttribute('password');
    }

    public function changePassword(string $password): self
    {
        $this->setAttribute('password', $password);

        return $this;
    }

    public function state(): int
    {
        return $this->getAttribute('user_state');
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('user_state', $state);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function createdAt(): ?DateTime
    {
        $dateTime = $this->getAttribute('created_at');

        return ($dateTime) ? $this->getDateTime($dateTime) : $dateTime;

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

    public function changeUpdatedAt(?DateTime $datetime): self
    {
        $this->setAttribute('updated_at', $datetime);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function deletedAt(): ?DateTime
    {
        $datetime = $this->getAttribute('deleted_at');

        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeDeletedAt(?DateTime $datetime): self
    {
        $this->setAttribute('deleted_at', $datetime);

        return $this;
    }

    public function photo(): ?string
    {
        return $this->getAttribute('user_photo');
    }

    public function changePhoto(?string $photo): self
    {
        $this->setAttribute('user_photo', $photo);

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
