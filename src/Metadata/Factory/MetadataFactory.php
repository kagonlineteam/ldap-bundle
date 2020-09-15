<?php

namespace KAGOnlineTeam\LdapBundle\Metadata\Factory;

use function class_exists;
use InvalidArgumentException;
use KAGOnlineTeam\LdapBundle\Exception\NoMetadataException;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadata;
use KAGOnlineTeam\LdapBundle\Metadata\ClassMetadataInterface;
use KAGOnlineTeam\LdapBundle\Metadata\Extractor\ExtractorInterface;
use function sprintf;

/**
 * The main metadata factory which creates the metadata for a class by running
 * a chain of loaders.
 *
 * @author Jan Flaßkamp
 */
class MetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var ExtractorInterface[]
     */
    private $extractorChain;

    public function __construct(iterable $extractorChain)
    {
        $this->extractorChain = $extractorChain;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $class): ClassMetadataInterface
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('The class "%s" does not exist.', $class));
        }

        $metadata = new ClassMetadata($class);

        foreach ($this->extractorChain as $extractor) {
            try {
                $extractor->extractFor($metadata);

                return $metadata;
            } catch (NoMetadataException $e) {
                continue;
            }
        }

        throw new NoMetadataException(sprintf('Metadata for class "%s" could not be loaded.', $class));
    }
}
