<?php

namespace Core\Campus\Infrastructure\Persistence\Eloquent\Model;

use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'campus';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'cam_id';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'cam_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cam_id',
        'cam__inst_id',
        'cam_name',
        'cam_address',
        'cam_phone',
        'cam_email',
        'cam_observations',
        'cam_search',
        'cam_state',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'cam_search';

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
        return $this->belongsTo(Institution::class, 'cam__inst_id', 'inst_id');
    }

    public function institution(): Model
    {
        return $this->relationWithInstitution()->getModel();
    }

    public function id(): ?int
    {
        /** @var int|null $id */
        $id = $this->getAttribute('cam_id');

        return $id;
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('cam_id', $id);

        return $this;
    }

    public function institutionId(): int
    {
        /** @var int $id */
        $id = $this->getAttribute('cam__inst_id');

        return $id;
    }

    public function changeInstitutionId(int $id): self
    {
        $this->setAttribute('cam__inst_id', $id);

        return $this;
    }

    public function name(): string
    {
        /** @var string $name */
        $name = $this->getAttribute('cam_name');

        return $name;
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('cam_name', $name);

        return $this;
    }

    public function address(): ?string
    {
        /** @var string|null $address */
        $address = $this->getAttribute('cam_address');

        return $address;
    }

    public function changeAddress(string $address): self
    {
        $this->setAttribute('cam_address', $address);

        return $this;
    }

    public function phone(): ?string
    {
        /** @var string|null $phone */
        $phone = $this->getAttribute('cam_phone');

        return $phone;
    }

    public function changePhone(?string $phone = null): self
    {
        $this->setAttribute('cam_phone', $phone);

        return $this;
    }

    public function email(): ?string
    {
        /** @var string|null $email */
        $email = $this->getAttribute('cam_email');

        return $email;
    }

    public function changeEmail(?string $email = null): self
    {
        $this->setAttribute('cam_email', $email);

        return $this;
    }

    public function observations(): ?string
    {
        /** @var string|null $observations */
        $observations = $this->getAttribute('cam_observations');

        return $observations;
    }

    public function changeObservations(?string $observations = null): self
    {
        $this->setAttribute('cam_observations', $observations);

        return $this;
    }

    public function search(): ?string
    {
        /** @var string|null $search */
        $search = $this->getAttribute('cam_search');

        return $search;
    }

    public function changeSearch(?string $search = null): self
    {
        $this->setAttribute('cam_search', $search);

        return $this;
    }

    public function state(): int
    {
        /** @var int $state */
        $state = $this->getAttribute('cam_state');

        return $state;
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('cam_state', $state);

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

    /**
     * @throws \Exception
     */
    private function getDateTime(string $datetime = 'now'): \DateTime
    {
        return new \DateTime($datetime);
    }
}
