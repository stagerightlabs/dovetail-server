<?php

namespace App;

class AccessLevel
{
    /**
     * Access Level Tiers
     */
    public static $SUPER_ADMIN = 1000;
    public static $ORGANIZATION_ADMIN = 800;
    public static $ORGANIZATION_MEMBER = 600;
    public static $ORGANIZATION_READ_ONLY = 200;

    /**
     * Convert an access level into a representational string
     *
     * @param int $level
     * @return string
     */
    public static function rank($level)
    {
        switch ($level) {
            case self::$SUPER_ADMIN:
                return 'Super Admin';
                break;

            case self::$ORGANIZATION_ADMIN:
                return 'Admin';
                break;

            case self::$ORGANIZATION_MEMBER:
                return 'Member';

            default:
                return 'None';
                break;
        }
    }
}
