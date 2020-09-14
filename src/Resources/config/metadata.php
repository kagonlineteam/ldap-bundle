<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use KAGOnlineTeam\LdapBundle\Metadata\Extractor\AnnotationExtractor;
use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactoryInterface;
use KAGOnlineTeam\LdapBundle\Metadata\Factory\MetadataFactory;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(AnnotationExtractor::class, AnnotationExtractor::class)
            ->args([
                service('annotations.reader'),
            ])
        
        ->set(MetadataFactoryInterface::class, MetadataFactory::class)
            ->args([
                [service(AnnotationExtractor::class)],
            ])
    ;
};
