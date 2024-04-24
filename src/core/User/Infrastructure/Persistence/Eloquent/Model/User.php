<?php

namespace Core\User\Infrastructure\Persistence\Eloquent\Model;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Core\Employee\Infrastructure\Persistence\Eloquent\Model\Employee;
use Core\Profile\Infrastructure\Persistence\Eloquent\Model\Profile;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

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
     * @var array
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
        'password',
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
    ];

    protected Employee $relationWithEmployee;
    protected Profile $relationWithProfile;
    protected $touches = ['relationWithEmployee','relationWithProfile'];

    /**
     * The search field associated with the table.
     *
     * @var string
     */
    protected string $mainSearchField = 'user_login';

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    public function relationWithEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'user__emp_id','emp_id');
    }

    public function employee(): Employee
    {
        return $this->relationWithEmployee;
    }

    public function relationWithProfile(): BelongsTo
    {
        return $this->belongsTo(Profile::class,'user__pro_id','pro_id');
    }

    public function profile(): Profile
    {
        return $this->relationWithProfile;
    }

    public function id(): null|int
    {
        return $this->attributes['user_id'];
    }

    public function changeId(?int $id): void
    {
        $this->attributes['user_id'] = $id;
    }

    public function employeeId(): null|int
    {
        return $this->attributes['user__emp_id'];
    }

    public function changeEmployeeId(int $id): void
    {
        $this->attributes['user__emp_id'] = $id;
    }

    public function profileId(): null|int
    {
        return $this->attributes['user__pro_id'];
    }

    public function changeProfileId(int $id): void
    {
        $this->attributes['user__pro_id'] = $id;
    }

    public function login(): null|string
    {
        return $this->attributes['user_login'];
    }

    public function changeLogin(string $login): void
    {
        $this->attributes['user_login'] = $login;
    }

    public function password(): null|string
    {
        return $this->attributes['password'];
    }

    public function changePassword(string $password): void
    {
        $this->attributes['password'] = $password;
    }

    public function state(): null|int
    {
        return $this->attributes['user_state'];
    }

    public function changeState(int $state): void
    {
        $this->attributes['user_state'] = $state;
    }

    /**
     * @throws Exception
     */
    public function createdAt(): null|DateTime
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
    public function updatedAt(): null|DateTime
    {
        return ($this->attributes['updated_at']) ? $this->getDateTime($this->attributes['updated_at']) : $this->attributes['updated_at'];

    }

    public function changeUpdatedAt(null|DateTime $datetime): void
    {
        $this->attributes['updated_at'] = $datetime;
    }

    public function photo(): null|string
    {
        return $this->attributes['user_photo'];
    }

    public function changePhoto(?string $photo): void
    {
        $this->attributes['user_photo'] = $photo;
    }

    /**
     * @throws Exception
     */
    private function getDateTime(?string $datetime = null): DateTime
    {
        return new DateTime($datetime);
    }
}
