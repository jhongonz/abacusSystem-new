<?php

namespace Core\Institution\Infrastructure\Persistence\Eloquent\Model;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstitutionContactCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'institutions_contact_card';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'card_id';

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'card_state' => 1,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'card_id',
        'card_phone',
        'card_email',
        'card_contact_person',
        'card_default',
        'card_observations',
        'card_state',
        'card_search',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The search field associated with the table.
     */
    protected string $mainSearchField = 'card_search';

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
        return $this->belongsTo(Institution::class, 'card__inst_id', 'inst_id');
    }

    public function id(): ?int
    {
        return $this->getAttribute('card_id');
    }

    public function changeId(?int $id): self
    {
        $this->setAttribute('card_id', $id);
        return $this;
    }

    public function phone(): string
    {
        return $this->getAttribute('card_phone');
    }

    public function changePhone(string $phone): self
    {
        $this->setAttribute('card_phone', $phone);
        return $this;
    }

    public function email(): ?string
    {
        return $this->getAttribute('card_email');
    }

    public function changeEmail(?string $email): self
    {
        $this->setAttribute('card_email', $email);
        return $this;
    }

    public function contactPerson(): ?string
    {
        return $this->getAttribute('card_contact_person');
    }

    public function changeContactPerson(?string $person): self
    {
        $this->setAttribute('card_contact_person', $person);
        return $this;
    }

    public function contactDefault(): int
    {
        return $this->getAttribute('card_default');
    }

    public function changeContactDefault(int $value): self
    {
        $this->setAttribute('card_default', $value);
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
