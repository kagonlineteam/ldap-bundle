<?php

namespace KAGOnlineTeam\LdapBundle\Annotation;

/**
 * Entry annotation for classes.
 *
 * @author Jan Flaßkamp
 *
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes(
 *     @Attribute("repositoryClass", type="string, required=true),
 *     @Attribute("objectClasses", type="array", required=true)
 * )
 */
class Entry
{
    public $repositoryClass;
    public $objectClasses;
}
