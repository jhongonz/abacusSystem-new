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

    protected $touches = ['relationWithUser'];

    /**
     * The search field associated with the table.
     *
     * @var string
     */
    protected string $mainSearchField = 'emp_search';

    public function getSearchField(): string
    {
        return $this->mainSearchField;
    }

    public function relationWithUser(): HasOne
    {
        return $this->hasOne(User::class,'user__emp_id','emp_id');
    }

    public function user(): Model
    {
        return $this->relationWithUser()->getModel();
    }

    public function id(): null|int
    {
        return $this->getAttribute('emp_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('emp_id', $id);
        return $this;
    }

    public function identification(): null|string
    {
        return $this->getAttribute('emp_identification');
    }

    public function changeIdentification(string $identification): self
    {
        $this->setAttribute('emp_identification', $identification);
        return $this;
    }

    public function name(): null|string
    {
        return $this->getAttribute('emp_name');
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('emp_name', $name);
        return $this;
    }

    public function lastname(): null|string
    {
        return $this->getAttribute('emp_lastname');
    }

    public function changeLastname(string $lastname): self
    {
        $this->setAttribute('emp_lastname', $lastname);
        return $this;
    }

    public function phone(): null|string
    {
        return $this->getAttribute('emp_phone_number');
    }

    public function changePhone(string $phone): self
    {
        $this->setAttribute('emp_phone_number', $phone);
        return $this;
    }

    public function email(): null|string
    {
        return $this->getAttribute('emp_email');
    }

    public function changeEmail(string $email): self
    {
        $this->setAttribute('emp_email', $email);
        return $this;
    }

    public function address(): null|string
    {
        return $this->getAttribute('emp_address');
    }

    public function changeAddress(string $address): self
    {
        $this->setAttribute('emp_address', $address);
        return $this;
    }

    public function state(): int
    {
        return $this->getAttribute('emp_state');
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('emp_state', $state);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function createdAt(): null|DateTime
    {
        $datetime = $this->getAttribute('created_at');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeCreatedAt(?DateTime $datetime): self
    {
        $this->setAttribute('created_at', $datetime);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function updatedAt(): null|DateTime
    {
        $datetime = $this->getAttribute('updated_at');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeUpdatedAt(?DateTime $datetime): self
    {
        $this->setAttribute('updated_at', $datetime);
        return $this;
    }

    public function search(): null|string
    {
        return $this->getAttribute('emp_search');
    }

    public function changeSearch(string $search): self
    {
        $this->setAttribute('emp_search', $search);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function birthdate(): null|DateTime
    {
        $datetime = $this->getAttribute('emp_birthdate');
        return ($datetime) ? $this->getDateTime($datetime) : $datetime;
    }

    public function changeBirthdate(?DateTime $date): self
    {
        $this->setAttribute('emp_birthdate', $date);
        return $this;
    }

    public function observations(): null|string
    {
        return $this->getAttribute('emp_observations');
    }

    public function changeObservations(null|string $observations): self
    {
        $this->setAttribute('emp_observations', $observations);
        return $this;
    }

    public function identificationType(): string
    {
        return $this->getAttribute('emp_identification_type');
    }

    public function changeIdentificationType(string $type): self
    {
        $this->setAttribute('emp_identification_type', $type);
        return $this;
    }

    public function image(): null|string
    {
        return $this->getAttribute('emp_image');
    }

    public function changeImage(?string $image): self
    {
        $this->setAttribute('emp_image', $image);
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
