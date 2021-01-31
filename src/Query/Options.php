<?php

namespace KAGOnlineTeam\LdapBundle\Query;

use Symfony\Component\Ldap\Adapter\QueryInterface;

class Options
{
    const BASE_DN = 'query.base_dn';

    const SCOPE_BASE = QueryInterface::SCOPE_BASE;
    const SCOPE_ONE = QueryInterface::SCOPE_ONE;
    const SCOPE_SUB = QueryInterface::SCOPE_SUB;

    const DEREF_NEVER = QueryInterface::DEREF_NEVER;
    const DEREF_SEARCHING = QueryInterface::DEREF_SEARCHING;
    const DEREF_FINDING = QueryInterface::DEREF_FINDING;
    const DEREF_ALWAYS = QueryInterface::DEREF_ALWAYS;

    public static function assertScope(string $scope): void
    {
        if (!\in_array($scope, [self::SCOPE_BASE, self::SCOPE_ONE, self::SCOPE_SUB])) {
            throw new \InvalidArgumentException(sprintf('Undefined scope "%s"', $scope));
        }
    }

    public static function assertDeref(int $deref): void
    {
        if (!\in_array($deref, [
            self::DEREF_NEVER, self::DEREF_SEARCHING, self::DEREF_FINDING, self::DEREF_ALWAYS,
        ])) {
            throw new \InvalidArgumentException(sprintf('Undefined deref "%s"', $deref));
        }
    }

    public static function assertTimeout(int $timelimit): void
    {
        if ($timelimit < 0) {
            throw new \InvalidArgumentException(sprintf('Invalid time limit: %d', $timelimit));
        }
    }

    public static function assertLimit(int $limit): void
    {
        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be greater than 0');
        }
    }

    public static function assertFilter(string $filter): void
    {
    }
}
