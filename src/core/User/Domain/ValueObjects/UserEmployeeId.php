<?php

namespace Core\User\Domain\ValueObjects;

use Core\SharedContext\Model\ValueObjectContract;

class UserEmployeeId implements ValueObjectContract
{
    private null|int $value;
    
    public function __construct(null|int $value = null)
    {
        if (!is_null($value)) $this->validate($value);
        $this->value = $value;
    }
    
    public function value(): null|int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
    
    private function validate(int $id): void
    {
        $options = [
            'options' => [
                'min_range' => 1,
            ]
        ];

        if (!filter_var($id, FILTER_VALIDATE_INT, $options)) {
            throw new \InvalidArgumentException(
                sprintf('<%s> does not allow the value <%s>.', static::class, $id)
            );
        }
    }
}