<?php

namespace KAGOnlineTeam\LdapBundle\Annotation;

/**
 * Attribute annotation for properties.
 *
 * @author Jan Flaßkamp
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes(
 *     @Attribute("description", type="string", required=true),
 *     @Attribute("type", type="string", required=true),
 * )
 */
class Attribute
{
    public $description;

    /**
     * @Enum({"array", "scalar", "multivalue"})
     */
    public $type;
}
