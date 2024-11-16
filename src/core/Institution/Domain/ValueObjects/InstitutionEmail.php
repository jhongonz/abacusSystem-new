<?php
/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-06-16 20:31:19
 */

namespace Core\Institution\Domain\ValueObjects;

class InstitutionEmail
{
    public function __construct(
        private ?string $value = null,
    ) {
        if (!is_null($value)) {
            $this->validate($value);
            $this->setValue($value);
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->validate($value);
        $this->value = $value;

        return $this;
    }

    private function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('<%s> does not allow the invalid email: <%s>.', static::class, $value));
        }
    }
}
