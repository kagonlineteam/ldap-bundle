<?php

namespace KAGOnlineTeam\LdapBundle\Attribute;

/**
 * An OOP implementation of a distinguished name of a LDAP entry. 
 * 
 * @author Jan Flaßkamp
 */
class DistinguishedName 
{
    /**
     * Holds all information of the original DN string in an array.
     * 
     * The array consists of zero or more RDNs which are arrays by themselves.
     * To support multivalue RDNs a single RDN is an array of name-value pairs.
     * E.g. the dn "cn=John+employeeNumber=1,ou=users,ou=system" results in:
     * [
     *     [["cn", "John"], ["employeeNumber", "1"]],
     *     [["ou", "users"]],
     *     [["ou", "system"]]
     * ]
     */
    protected $rdns = [];

    public function __construct(array $rdns = [])
    {
        $this->rdns = $rdns;
    }

    /**
     * Creates a new DN object from a DN string.
     * 
     * @return self
     */
    public static function deserialize(string $dn): self
    {
        $rdns = \ldap_explode_dn($dn, 0);

        if (\is_array($rdns) && \array_key_exists('count', $rdns)) {
            unset($rdns['count']);

            foreach ($rdns as $key => $rdn) {

                $rdns[$key] = [];
                // Handle multivalued RDNs.
                foreach (\explode('+', $rdn) as $value) {
                    $pos = \strpos($value, '=');
                    if (false === $pos) {
                        throw new \InvalidArgumentException(\sprintf('Expected "=" in RDN ("%s").', $value));
                    }

                    $name = \substr($value, 0, $pos);

                    // Unescape characters.
                    $value = \preg_replace_callback('/\\\([0-9A-Fa-f]{2})/', function ($matches) {
                        return \chr(\hexdec($matches[1]));
                    }, \substr($value, $pos+1));

                    $rdns[$key][] = [$name, $value];
                }
            }
        }

        return new self($rdns);
    }

    /**
     * Returns the string representation of the DN object.
     * 
     * @return string The DN string
     */
    public function serialize(): string
    {
        $rdns = [];

        foreach ($this->rdns as $rdn) {
            $rdnPairs = [];
            foreach ($rdn as $pair) {

                $value = \ldap_escape($pair[1], '', LDAP_ESCAPE_DN);
                if (!empty($value) && ' ' === $value[0]) {
                    $value = '\\20'.\substr($value, 1);
                }
                if (!empty($value) && ' ' === $value[\strlen($value) - 1]) {
                    $value = \substr($value, 0, -1).'\\20';
                }
                $value = \str_replace("\r", '\0d', $value);

                $rdnPairs[] = $pair[0].'='.$value;
            }

            $rdns[] = \implode('+', $rdnPairs);
        }

        return \implode(',', $rdns);
    }

    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * Returns all RDNs.
     * 
     * @return array An array of RDNs
     */
    public function all(): array
    {
        return $this->rdns;
    }

    /**
     * Returns the number of RDNs the DN contains. Multivalued RDNs will be
     * counted as one.
     * 
     * @return int
     */
    public function count(): int
    {
        return \count($this->rdns);
    }

    /**
     * Returns the first RDN as a string.
     * 
     * @return string
     */
    public function getRdn(): string
    {
        if (empty($this->rdns)) {
            throw new LogicException('The DN has zero RDNs.');
        }

        // Get the RDN through the serialization process of an object.
        $rdn = (new self($this->rdns[0]))
            ->serialize();

        return $rdn;
    }

    /**
     * Returns a new DistinguishedName object of the parent entry.
     * 
     * @throws LogicException If there is no parent entry
     * 
     * @return DistinguishedName A new DN object of the parent entry
     */
    public function getParent(): DistinguishedName
    {
        if (empty($this->rdns)) {
            throw new LogicException('Cannot get the parent DN of the root entry.');
        }

        return new DistinguishedName(\array_slice($this->rdns, 1));
    }

    /**
     * Changes into the DN of the parent.
     * 
     * @return $this
     */
    public function removeRdn(): self
    {
        if (!empty($this->rdns)) {
            $this->rdns = \array_slice($this->rdns, 1);
        }

        return $this;
    }

    /**
     * Extends the distinguished name by adding a new RDN.
     * 
     * @param array $pairs 
     * 
     * @return $this
     */
    public function addRdn(...$pairs): self
    {
        if (\count($pairs) < 1) {
            throw new \InvalidArgumentException('Expected at least one name-value pair.');
        }

        foreach ($pairs as $pair) {
            if (!\is_array($pair)) {
                throw new \InvalidArgumentException('A name-value pair must be of type array.');
            }
            
            if (\count($pair) !== 2) {
                throw new \InvalidArgumentException('A name-value pair must consist of exactly two elements.');
            }

            if (!\is_string($pair[0]) or !\is_string($pair[1])) {
                throw new \InvalidArgumentException('The name and value must be of type string.');
            }
        }

        \array_unshift($this->rdns, $pairs);

        return $this;
    }
}