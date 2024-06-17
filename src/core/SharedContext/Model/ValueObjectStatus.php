<?php

namespace Core\SharedContext\Model;

use Exception;

/**
 * @codeCoverageIgnore
 */
class ValueObjectStatus
{
    public const STATE_NEW = 1;

    public const STATE_ACTIVE = 2;

    public const STATE_INACTIVE = 3;

    public const STATE_DELETE = -1;

    public const STATE_DEFAULT = self::STATE_ACTIVE;

    public const REGISTRY_STATES = [
        self::STATE_NEW,
        self::STATE_ACTIVE,
        self::STATE_INACTIVE,
        self::STATE_DELETE,
    ];

    public const STYLE_LITERAL_STATE = [
        self::STATE_NEW => [
            'class' => 'badge-primary bg-orange-600',
            'literal' => 'Nuevo',
        ],
        self::STATE_ACTIVE => [
            'class' => 'badge-success',
            'literal' => 'Activo',
        ],
        self::STATE_INACTIVE => [
            'class' => 'badge-danger',
            'literal' => 'Inactivo',
        ],
    ];

    protected string $valueLiteral;

    protected int $value;

    /**
     * @throws Exception
     */
    public function __construct(int $value)
    {
        $this->validateState($value);
        $this->value = $value;

        $this->changeValueLiteral($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws Exception
     */
    public function setValue(int $value): self
    {
        $this->validateState($value);
        $this->value = $value;


        return $this;
    }

    public function getValueLiteral(): string
    {
        return $this->valueLiteral;
    }

    public function activate(): self
    {
        $this->value = self::STATE_ACTIVE;
        $this->changeValueLiteral(self::STATE_INACTIVE);

        return $this;
    }

    public function inactive(): self
    {
        $this->value = self::STATE_INACTIVE;
        $this->changeValueLiteral(self::STATE_INACTIVE);

        return $this;
    }

    public function isNew(): bool
    {
        return $this->value() === self::STATE_NEW;
    }

    public function isActivated(): bool
    {
        return $this->value() === self::STATE_ACTIVE;
    }

    public function isInactivated(): bool
    {
        return $this->value() === self::STATE_INACTIVE;
    }

    public function formatHtmlToState(): string
    {
        $state = $this->value;
        $style = self::STYLE_LITERAL_STATE[$state];

        return sprintf('<span class="badge %s">%s</span>', $style['class'], $this->valueLiteral);
    }

    protected function changeValueLiteral(int $state): self
    {
        if ($state !== self::STATE_DELETE) {
            $literal = self::STYLE_LITERAL_STATE[$state]['literal'];
            $this->valueLiteral = $literal;
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function validateState(int $value): void
    {
        if (! in_array($value, self::REGISTRY_STATES)) {
            throw new Exception(
                sprintf('<%s> does not allow the invalid state: <%s>.', static::class, $value)
            );
        }
    }
}
