<?php

namespace Core\Employee\Domain\ValueObjects;

use InvalidArgumentException;

class EmployeeInstitutionId
{
    public function __construct(
        private ?int $value = null
    ) {
        if (! is_null($value)) {
            $this->validate($value);
            $this->setValue($this->value);
        }
    }

    public function value(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->validate($value);
        $this->value = $value;

        return $this;
    }

    private function validate(int $value): void
    {
        $options = [
            'options' => [
                'min_range' => 1,
            ],
        ];

        if (! filter_var($value, FILTER_VALIDATE_INT, $options)) {
            throw new InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, $value)
            );
        }
    }
}
