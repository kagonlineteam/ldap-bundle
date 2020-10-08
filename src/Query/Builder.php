<?php

namespace KAGOnlineTeam\LdapBundle\Query;

use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Query\Filter\FilterInterface;
use KAGOnlineTeam\LdapBundle\Query\Filter\NestingTrait;
use KAGOnlineTeam\LdapBundle\Query\Filter\RawFilter;
use Symfony\Component\Ldap\Adapter\ExtLdap\Adapter;

/**
 * Builder class for LDAP queries.
 *
 * @author Jan Flaßkamp
 */
class Builder implements FilterInterface
{
    use NestingTrait;

    /**
     * @var string The configured base DN for queries
     */
    private $baseDn;

    /**
     * @var ClassMetadataInterface
     */
    private $metadata;

    /**
     * @var string The user entered dn
     */
    private $queryDn;

    /**
     * @var array Options array to configure the resolve process of a query
     */
    private $options = [
        'resolve_properties' => true,
        'append_objectclasses' => true,
    ];

    /**
     * @var array Options for the Ldap query
     */
    private $queryOptions = [];

    /**
     * @var FilterInterface The filter of the query
     */
    private $child;

    /**
     * @var array Simplified mapping from object properties to Ldap attributes
     */
    private $attributeMap = [];

    public function __construct(string $baseDn, ClassMetadataInterface $metadata)
    {
        $this->baseDn = $baseDn;
        $this->metadata = $metadata;
    }

    /**
     * Sets a dn as search base. Use Options::BASE_DN to indicate that the
     * configured base dn should be used.
     *
     * @param string $dn A valid dn or Options::BASE_DN
     *
     * @return $this
     */
    public function in(string $dn): self
    {
        $this->queryDn = $queryDn;

        return $this;
    }

    /**
     * Sets a new scope for the Ldap query.
     *
     * @param string $scope A scope from the Options::SCOPE_* constants
     */
    public function scope(string $scope): self
    {
        Options::assertScope($scope);
        $this->queryOptions['scope'] = $scope;

        return $this;
    }

    /**
     * Sets a new deref value for the Ldap query.
     *
     * @param string $deref A deref value from the Options::DEREF_* constants
     */
    public function deref(int $deref): self
    {
        Options::assertDeref($deref);
        $this->queryOptions['deref'] = $deref;

        return $this;
    }

    /**
     * @param int $timeout A new timeout value in seconds
     */
    public function timeout(int $timeout): self
    {
        Options::assertTimeout($timeout);
        $this->queryOptions['timeout'] = $timeout;

        return $this;
    }

    public function limit(int $limit): self
    {
        Options::assertLimit($limit);
        $this->queryOptions['maxItems'] = $limit;

        return $this;
    }

    /**
     * By default the value will be true, this means the queried attributes
     * (class properties) will be replaced by the mapped Ldap attributes.
     *
     * @param bool $value false if the queried attributes should be used
     *
     * @return $this
     */
    public function resolveProperties(bool $value): self
    {
        $this->options['resolve_properties'] = $value;

        return $this;
    }

    /**
     * If this is set to true, a presence filter for all necessary objectClasses will
     * be added to the query.
     *
     * @param bool $value false if the objectClasses should not be added
     *
     * @return $this
     */
    public function appendObjectClasses(bool $value): self
    {
        $this->options['append_objectclasses'] = $value;

        return $this;
    }

    /**
     * @return Query The configured query from this builder intance
     */
    public function make(): Query
    {
        $filter = $this->resolve([$this, 'getAttribute'], [self::class, 'escape']);

        if ($this->options['append_objectclasses']) {
            $objFilter = $this->filterAnd();

            foreach ($this->metadata->getObjectClasses() as $objectClass) {
                $objFilter->filterEquality()
                    ->with('objectClass', $objectClass)
                ->end();
            }

            $rawFilter = (new RawFilter($objFilter, FilterInterface::UNSPECIFIED))
                ->from($filter);

            $filter = $objFilter->resolve(function (string $property) {
                return $property;
            }, [self::class, 'escape']);
        }

        return new Query(
            (isset($this->dn) and Options::BASE_DN !== $this->dn) ? $this->dn : $this->baseDn,
            $filter,
            $this->queryOptions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return FilterInterface::BUILDER;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(callable $attrCall, callable $escCall): string
    {
        return $this->child->resolve($attrCall, $escCall);
    }

    /**
     * Returns $this as there is no parent object.
     *
     * {@inheritdoc}
     */
    public function end(): FilterInterface
    {
        return $this;
    }

    /**
     * Returns an Ldap attribute from a given object property name.
     *
     * @param string The property name
     *
     * @return string The Ldap attribute
     */
    public function getAttribute(string $property): string
    {
        $this->metadata->getClass();
        if (!$this->options['resolve_properties']) {
            return $property;
        }

        if (empty($this->attributeMap)) {
            foreach ($this->metadata->getProperties() as $propertyMetadata) {
                $this->attributeMap[$propertyMetadata->getName()] = $propertyMetadata->getAttribute();
            }
        }

        if (!\array_key_exists($property, $this->attributeMap)) {
            throw new \RuntimeException(sprintf('Undefined property name "%s"', $property));
        }

        return $this->attributeMap[$property];
    }

    /**
     * Escapes a given string to safely use it in a filter.
     *
     * @param string $value The unescaped value
     *
     * @return string The escaped value
     */
    public static function escape(string $value): string
    {
        if ('' === $value) {
            return '\\00';
        }

        return (new Adapter())
            ->escape($value, '', LDAP_ESCAPE_FILTER);
    }

    public function addChild(FilterInterface $child): void
    {
        $this->child = $child;
    }
}
