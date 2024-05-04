<?php

namespace Core\SharedContext\Model;

use Exception;

/**
 * @codeCoverageIgnore
 */
class ValueObjectStatus implements ValueObjectContract
{
    const STATE_NEW = 1;
    const STATE_ACTIVE = 2;
    const STATE_INACTIVE = 3;
    const STATE_DELETE = -1;

    const STATE_DEFAULT = self::STATE_ACTIVE;
    const REGISTRY_STATES = [
        self::STATE_NEW,
        self::STATE_ACTIVE,
        self::STATE_INACTIVE,
        self::STATE_DELETE
    ];

    const STYLE_LITERAL_STATE = [
        self::STATE_NEW => [
            'class' => 'badge-primary bg-orange-600',
            'literal' => 'Nuevo'
        ],
        self::STATE_ACTIVE => [
            'class' => 'badge-success',
            'literal' => 'Activo'
        ],
        self::STATE_INACTIVE => [
            'class' => 'badge-danger',
            'literal' => 'Inactivo'
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

        $this->valueLiteral = self::STYLE_LITERAL_STATE[$value]['literal'];
    }

    public function value(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @throws Exception
     */
    public function setValue($value): self
    {
        $this->validateState($value);
        $this->value = $value;

        $this->changeValueLiteral(self::STYLE_LITERAL_STATE[$value]['literal']);

        return $this;
    }

    public function getValueLiteral(): string
    {
        return  $this->valueLiteral;
    }

    public function activate(): self
    {
        $this->value = self::STATE_ACTIVE;
        $this->changeValueLiteral(self::STYLE_LITERAL_STATE[self::STATE_ACTIVE]['literal']);

        return $this;
    }

    public function inactive(): self
    {
        $this->value = self::STATE_INACTIVE;
        $this->changeValueLiteral(self::STYLE_LITERAL_STATE[self::STATE_INACTIVE]['literal']);

        return $this;
    }

    public function isNew(): bool
    {
        return ($this->value() === self::STATE_NEW);
    }

    public function isActivated(): bool
    {
        return ($this->value() === self::STATE_ACTIVE);
    }

    public function isInactivated(): bool
    {
        return ($this->value() === self::STATE_INACTIVE);
    }

    public function formatHtmlToState(): string
    {
        $state = $this->value();
        $style = self::STYLE_LITERAL_STATE[$state];

        return sprintf('<span class="badge %s">%s</span>', $style['class'], $this->getValueLiteral());
    }

    protected function changeValueLiteral(string $literal): self
    {
        $this->valueLiteral = $literal;

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function validateState(int $value): void
    {
        if (!in_array($value, self::REGISTRY_STATES)) {
            throw new Exception(
                sprintf('<%s> does not allow the invalid state: <%s>.', static::class, $value)
            );
        }
    }
}
