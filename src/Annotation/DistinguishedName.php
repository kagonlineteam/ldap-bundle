<?php

namespace KAGOnlineTeam\LdapBundle\Annotation;

/**
 * Distinguished name annotation.
 *
 * @author Jan Flaßkamp
 *
 * @Annotation
 * @Target({"PROPERTY"})
 * @Attributes(
 *     @Attribute("type", type="string", required=true),
 * )
 */
class DistinguishedName
{
    /**
     * @Enum({"string", "object"})
     */
    public $type;
}
