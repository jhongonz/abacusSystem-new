<?php

namespace App\Models;

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
        'emp_identification',
        'emp_name',
        'emp_lastname',
        'emp_phone_number',
        'emp_birthdate',
        'emp_email',
        'emp_address',
        'emp_observations',
        'emp_search',
        'emp_state',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function relationWithUser(): HasOne
    {
        return $this->hasOne(User::class,'user__emp_id','emp_id');
    }

    public function user(): User
    {
        return $this->relationWithUser;
    }

    public function id(): null|int
    {
        return $this->attributes['emp_id'];
    }

    public function changeId(int $id): void
    {
        $this->attributes['emp_id'] = $id;
    }

    public function identification(): null|string
    {
        return $this->attributes['emp_identification'];
    }

    public function changeIdentification(string $identification): void
    {
        $this->attributes['emp_identification'] = $identification;
    }

    public function name(): null|string
    {
        return $this->attributes['emp_name'];
    }

    public function changeName(string $name): void
    {
        $this->attributes['emp_name'] = $name;
    }

    public function lastname(): null|string
    {
        return $this->attributes['emp_lastname'];
    }

    public function changeLastname(string $lastname): void
    {
        $this->attributes['emp_lastname'] = $lastname;
    }

    public function phone(): null|string
    {
        return $this->attributes['emp_phone_number'];
    }

    public function changePhone(string $phone): void
    {
        $this->attributes['emp_phone_number'] = $phone;
    }

    public function email(): null|string
    {
        return $this->attributes['emp_email'];
    }

    public function changeEmail(string $email): void
    {
        $this->attributes['emp_email'] = $email;
    }

    public function address(): null|string
    {
        return $this->attributes['emp_address'];
    }

    public function changeAddress(string $address): void
    {
        $this->attributes['emp_address'] = $address;
    }

    public function state(): null|int
    {
        return $this->attributes['emp_state'];
    }

    public function changeState(int $state): void
    {
        $this->attributes['emp_state'] = $state;
    }

    public function createdAt(): null|string
    {
        return $this->attributes['created_at'];
    }

    public function changeCreatedAt(DateTime $datetime): void
    {
        $this->attributes['created_at'] = $datetime;
    }

    public function updatedAt(): null|string
    {
        return $this->attributes['updated_at'];
    }

    public function changeUpdatedAt(DateTime $datetime): void
    {
        $this->attributes['updated_at'] = $datetime;
    }

    public function search(): null|string
    {
        return $this->attributes['emp_search'];
    }

    public function changeSearch(string $search): void
    {
        $this->attributes['emp_search'] = $search;
    }

    /**
     * @throws Exception
     */
    public function birthdate(): null|DateTime
    {
        return new DateTime($this->attributes['emp_birthdate']);
    }

    public function changeBirthdate(DateTime $date): void
    {
        $this->attributes['emp_birthdate'] = $date;
    }

    public function observations(): null|string
    {
        return $this->attributes['emp_observations'];
    }

    public function changeObservations(null|string $observations): void
    {
        $this->attributes['emp_observations'] = $observations;
    }
}
