<?php

namespace Core\Employee\Infrastructure\Persistence\Eloquent\Model;

use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution;
use Core\User\Infrastructure\Persistence\Eloquent\Model\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $relationWithUser
 */
class Employee extends Model
{
    use SoftDeletes;

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
     * @var array<string, int>
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
        'emp__inst_id',
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

    /** @var string[] */
    protected $touches = ['relationWithUser'];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'emp_search';

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
     * @return array<string, string>
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
     * @return BelongsTo<Institution, $this>
     */
    public function relationWithInstitution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'emp__inst_id', 'inst_id');
    }

    /**
     * @return HasOne<User, $this>
     */
    public function relationWithUser(): HasOne
    {
        return $this->hasOne(User::class, 'user__emp_id', 'emp_id');
    }

    public function user(): Model
    {
        return $this->relationWithUser()->getModel();
    }

    public function id(): ?int
    {
        /** @var int|null $id */
        $id = $this->getAttribute('emp_id');

        return $id;
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('emp_id', $id);

        return $this;
    }

    public function institutionId(): ?int
    {
        /** @var int|null $id */
        $id = $this->getAttribute('emp__inst_id');

        return $id;
    }

    public function changeInstitutionId(int $institutionId): self
    {
        $this->setAttribute('emp__inst_id', $institutionId);

        return $this;
    }

    public function identification(): ?string
    {
        /** @var string|null $identification */
        $identification = $this->getAttribute('emp_identification');

        return $identification;
    }

    public function changeIdentification(string $identification): self
    {
        $this->setAttribute('emp_identification', $identification);

        return $this;
    }

    public function name(): ?string
    {
        /** @var string|null $name */
        $name = $this->getAttribute('emp_name');

        return $name;
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('emp_name', $name);

        return $this;
    }

    public function lastname(): ?string
    {
        /** @var string|null $lastname */
        $lastname = $this->getAttribute('emp_lastname');

        return $lastname;
    }

    public function changeLastname(string $lastname): self
    {
        $this->setAttribute('emp_lastname', $lastname);

        return $this;
    }

    public function phone(): ?string
    {
        /** @var string|null $phone */
        $phone = $this->getAttribute('emp_phone_number');

        return $phone;
    }

    public function changePhone(string $phone): self
    {
        $this->setAttribute('emp_phone_number', $phone);

        return $this;
    }

    public function email(): ?string
    {
        /** @var string|null $email */
        $email = $this->getAttribute('emp_email');

        return $email;
    }

    public function changeEmail(string $email): self
    {
        $this->setAttribute('emp_email', $email);

        return $this;
    }

    public function address(): ?string
    {
        /** @var string|null $address */
        $address = $this->getAttribute('emp_address');

        return $address;
    }

    public function changeAddress(?string $address): self
    {
        $this->setAttribute('emp_address', $address);

        return $this;
    }

    public function state(): int
    {
        /** @var int $state */
        $state = $this->getAttribute('emp_state');

        return $state;
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('emp_state', $state);

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

    public function changeCreatedAt(?\DateTime $datetime): self
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

    public function search(): ?string
    {
        /** @var string|null $search */
        $search = $this->getAttribute('emp_search');

        return $search;
    }

    public function changeSearch(string $search): self
    {
        $this->setAttribute('emp_search', $search);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function birthdate(): ?\DateTime
    {
        /** @var string|null $datetime */
        $datetime = $this->getAttribute('emp_birthdate');

        return null !== $datetime ? $this->getDateTime($datetime) : null;
    }

    public function changeBirthdate(?\DateTime $date): self
    {
        $this->setAttribute('emp_birthdate', $date);

        return $this;
    }

    public function observations(): ?string
    {
        /** @var string|null $observations */
        $observations = $this->getAttribute('emp_observations');

        return $observations;
    }

    public function changeObservations(?string $observations): self
    {
        $this->setAttribute('emp_observations', $observations);

        return $this;
    }

    public function identificationType(): string
    {
        /** @var string $identificationType */
        $identificationType = $this->getAttribute('emp_identification_type');

        return $identificationType;
    }

    public function changeIdentificationType(string $type): self
    {
        $this->setAttribute('emp_identification_type', $type);

        return $this;
    }

    public function image(): ?string
    {
        /** @var string $image */
        $image = $this->getAttribute('emp_image');

        return $image;
    }

    public function changeImage(?string $image): self
    {
        $this->setAttribute('emp_image', $image);

        return $this;
    }

    /**
     * @throws \Exception
     */
    private function getDateTime(?string $datetime = null): \DateTime
    {
        return new \DateTime($datetime ?? 'now');
    }
}
