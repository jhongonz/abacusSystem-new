<?php

namespace Core\Employee\Infrastructure\Persistence\Eloquent\Model;

use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $relationWithUser
 */
class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'emp_id';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'emp_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'emp_id',
        'emp_identification',
        'emp_identification_type',
        'emp_name',
        'emp_lastname',
        'emp_phone_number',
        'emp_birthdate',
        'emp_email',
        'emp_address',
        'emp_observations',
        'emp_image',
        'emp_search',
        'emp_state',
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
        'deleted_at' => 'datetime'
    ];

    protected User $relationWithUser;
    protected $touches = ['relationWithUser'];

    /**
     * The search field associated with the table.
     *
     * @var string
     */
    protected string $mainSearchField = 'emp_search';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    public function relationWithUser(): HasOne
    {
        return $this->hasOne(User::class,'user__emp_id','emp_id');
    }

    public function user(): ?User
    {
        return $this->relationWithUser;
    }

    public function id(): null|int
    {
        return $this->getAttribute('emp_id');
    }

    public function changeId(?int $id): void
    {
        $this->setAttribute('emp_id', $id);
    }

    public function identification(): null|string
    {
        return $this->getAttribute('emp_identification');
    }

    public function changeIdentification(string $identification): void
    {
        $this->setAttribute('emp_identification', $identification);
    }

    public function name(): null|string
    {
        return $this->getAttribute('emp_name');
    }

    public function changeName(string $name): void
    {
        $this->setAttribute('emp_name', $name);
    }

    public function lastname(): null|string
    {
        return $this->getAttribute('emp_lastname');
    }

    public function changeLastname(string $lastname): void
    {
        $this->setAttribute('emp_lastname', $lastname);
    }

    public function phone(): null|string
    {
        return $this->getAttribute('emp_phone_number');
    }

    public function changePhone(string $phone): void
    {
        $this->setAttribute('emp_phone_number', $phone);
    }

    public function email(): null|string
    {
        return $this->getAttribute('emp_email');
    }

    public function changeEmail(string $email): void
    {
        $this->setAttribute('emp_email', $email);
    }

    public function address(): null|string
    {
        return $this->getAttribute('emp_address');
    }

    public function changeAddress(string $address): void
    {
        $this->setAttribute('emp_address', $address);
    }

    public function state(): null|int
    {
        return $this->getAttribute('emp_state');
    }

    public function changeState(int $state): void
    {
        $this->setAttribute('emp_state', $state);
    }

    /**
     * @throws Exception
     */
    public function createdAt(): null|DateTime
    {
        $datetime = $this->getAttribute('created_at');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeCreatedAt(?DateTime $datetime): void
    {
        $this->setAttribute('created_at', $datetime);
    }

    /**
     * @throws Exception
     */
    public function updatedAt(): null|DateTime
    {
        $datetime = $this->getAttribute('updated_at');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeUpdatedAt(?DateTime $datetime): void
    {
        $this->setAttribute('updated_at', $datetime);
    }

    public function search(): null|string
    {
        return $this->getAttribute('emp_search');
    }

    public function changeSearch(string $search): void
    {
        $this->setAttribute('emp_search', $search);
    }

    /**
     * @throws Exception
     */
    public function birthdate(): null|DateTime
    {
        $datetime = $this->getAttribute('emp_birthdate');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeBirthdate(?DateTime $date): void
    {
        $this->setAttribute('emp_birthdate', $date);
    }

    public function observations(): null|string
    {
        return $this->getAttribute('emp_observations');
    }

    public function changeObservations(null|string $observations): void
    {
        $this->setAttribute('emp_observations', $observations);
    }

    public function identificationType(): string
    {
        return $this->getAttribute('emp_identification_type');
    }

    public function changeIdentificationType(string $type): void
    {
        $this->setAttribute('emp_identification_type', $type);
    }

    public function image(): null|string
    {
        return $this->getAttribute('emp_image');
    }

    public function changeImage(?string $image): void
    {
        $this->setAttribute('emp_image', $image);
    }

    /**
     * @throws Exception
     */
    private function getDateTime(?string $datetime = null): DateTime
    {
        return new DateTime($datetime);
    }
}
