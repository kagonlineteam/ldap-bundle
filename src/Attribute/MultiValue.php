<?php

namespace KAGOnlineTeam\LdapBundle\Attribute;

/**
 * A basic implementation of an attribute which allows multiple values.
 *
 * @author Jan Flaßkamp
 */
class MultiValue
{
    private $original = [];
    private $values = [];

    public function __construct(array $values)
    {
        $this->original = $this->values = $values;
    }

    public static function deserialize(array $values)
    {
        return new self($values);
    }

    public function serialize(): array
    {
        return $this->values;
    }

    public function refresh(): self
    {
        $this->original = $this->values;

        return $this;
    }

    public function reset(): self
    {
        $this->values = $this->original;

        return $this;
    }

    public function getAll()
    {
        return $this->values;
    }

    public function getFirstAttribute($default = null)
    {
        if (empty($this->values)) {
            return $default;
        }

        $firstKey = array_keys($this->values)[0];

        return $this->values[$firstKey];
    }

    public function filter(string $pattern): array
    {
        $filtered = [];
        foreach ($this->values as $value) {
            if (preg_match($pattern, (string) $value)) {
                $filtered[] = $value;
            }
        }

        return $filtered;
    }

    public function implode(string $glue): string
    {
        return implode($glue, $this->values);
    }

    public function add(string $value): self
    {
        $key = array_search($value, $this->values, true);
        if (false !== $key) {
            throw new InvalidArgumentException('The value is already present.');
        }

        $this->values[] = $value;

        return $this;
    }

    public function remove(string $value): self
    {
        $key = array_search($value, $this->values, true);
        if (false === $key) {
            throw new InvalidArgumentException('The value does not exist.');
        }

        unset($this->values[$key]);

        return $this;
    }

    public function removeAll(): self
    {
        $this->values = [];

        return $this;
    }
}
