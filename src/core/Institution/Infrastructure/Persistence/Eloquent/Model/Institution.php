<?php

namespace Core\Institution\Infrastructure\Persistence\Eloquent\Model;

use Core\Campus\Infrastructure\Persistence\Eloquent\Model\Campus;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'institutions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'inst_id';

    /**
     * The model's default values for attributes.
     *
     * @var array<string, int>
     */
    protected $attributes = [
        'inst_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inst_id',
        'inst_code',
        'inst_name',
        'inst_shortname',
        'inst_logo',
        'inst_observations',
        'inst_state',
        'inst_search',
        'inst_address',
        'inst_phone',
        'inst_email',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'inst_search';

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

    public function relationWithCampus(): HasMany
    {
        return $this->hasMany(Campus::class, 'cam__inst_id', 'inst_id');
    }


    public function campus(): Model
    {
        return $this->relationWithCampus()->getModel();
    }

    public function id(): ?int
    {
        return $this->getAttribute('inst_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('inst_id', $id);
        return $this;
    }

    public function code(): ?string
    {
        return $this->getAttribute('inst_code');
    }

    public function changeCode(?string $code): self
    {
        $this->setAttribute('inst_code', $code);
        return $this;
    }

    public function name(): ?string
    {
        return $this->getAttribute('inst_name');
    }

    public function changeName(?string $name): self
    {
        $this->setAttribute('inst_name', $name);
        return $this;
    }

    public function shortname(): ?string
    {
        return $this->getAttribute('inst_shortname');
    }

    public function changeShortname(?string $shortname): self
    {
        $this->setAttribute('inst_shortname', $shortname);
        return $this;
    }

    public function logo(): ?string
    {
        return $this->getAttribute('inst_logo');
    }

    public function changeLogo(?string $logo): self
    {
        $this->setAttribute('inst_logo', $logo);
        return $this;
    }

    public function observations(): ?string
    {
        return $this->getAttribute('inst_observations');
    }

    public function changeObservations(?string $observations): self
    {
        $this->setAttribute('inst_observations', $observations);
        return $this;
    }

    public function address(): ?string
    {
        return $this->getAttribute('inst_address');
    }

    public function changeAddress(?string $address): self
    {
        $this->setAttribute('inst_address', $address);
        return $this;
    }

    public function phone(): ?string
    {
        return $this->getAttribute('inst_phone');
    }

    public function changePhone(string $phone): self
    {
        $this->setAttribute('inst_phone', $phone);
        return $this;
    }

    public function email(): ?string
    {
        return $this->getAttribute('inst_email');
    }

    public function changeEmail(?string $email): self
    {
        $this->setAttribute('inst_email', $email);
        return $this;
    }

    public function search(): ?string
    {
        return $this->getAttribute('inst_search');
    }

    public function changeSearch(string $search): self
    {
        $this->setAttribute('inst_search', $search);
        return $this;
    }

    public function state(): int
    {
        return $this->getAttribute('inst_state');
    }

    public function changeState(int $state): self
    {
        $this->setAttribute('inst_state', $state);
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
