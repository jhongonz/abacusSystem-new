<?php

namespace Core\Campus\Infrastructure\Persistence\Eloquent\Model;

use Core\Institution\Infrastructure\Persistence\Eloquent\Model\Institution;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory;
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
     * @var array
     */
    protected $attributes = [
        'cam_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
        return $this->getAttribute('cam_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('cam_id', $id);
        return $this;
    }

    public function institutionId(): int
    {
        return $this->getAttribute('cam__inst_id');
    }

    public function changeInstitutionId(int $id): self
    {
        $this->setAttribute('cam__inst_id', $id);
        return $this;
    }

    public function name(): string
    {
        return $this->getAttribute('cam_name');
    }

    public function changeName(string $name): self
    {
        $this->setAttribute('cam_name', $name);
        return $this;
    }

    public function address(): ?string
    {
        return $this->getAttribute('cam_address');
    }

    public function changeAddress(string $address): self
    {
        $this->setAttribute('cam_address', $address);
        return $this;
    }

    public function phone(): ?string
    {
        return $this->getAttribute('cam_phone');
    }

    public function changePhone(?string $phone): self
    {
        $this->setAttribute('cam_phone', $phone);
        return $this;
    }

    public function email(): ?string
    {
        return $this->getAttribute('cam_email');
    }

    public function changeEmail(?string $email): self
    {
        $this->setAttribute('cam_email', $email);
        return $this;
    }

    public function observations(): ?string
    {
        return $this->getAttribute('cam_observations');
    }

    public function changeObservations(?string $observations): self
    {
        $this->setAttribute('cam_observations', $observations);
        return $this;
    }

    public function search(): ?string
    {
        return $this->getAttribute('cam_search');
    }

    public function changeSearch(?string $search): self
    {
        $this->setAttribute('cam_search', $search);
        return $this;
    }

    public function state(): int
    {
        return $this->getAttribute('cam_state');
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('cam_state', $state);
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

    public function changeCreatedAt(?DateTime $datetime): self
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

    /**
     * @throws Exception
     */
    private function getDateTime(?string $datetime = null): DateTime
    {
        return new DateTime($datetime);
    }
}
