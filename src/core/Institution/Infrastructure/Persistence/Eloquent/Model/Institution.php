<?php

namespace Core\Institution\Infrastructure\Persistence\Eloquent\Model;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * @var array
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
    private function getDateTime(?string $datetime = null): DateTime
    {
        return new DateTime($datetime);
    }
}
