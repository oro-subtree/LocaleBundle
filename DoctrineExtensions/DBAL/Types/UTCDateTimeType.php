<?php

namespace Oro\Bundle\LocaleBundle\DoctrineExtensions\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    const TYPE = 'utc_date_time';

    /** @var null| \DateTimeZone  */
    static private $utc = null;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $sqlDeclaration = parent::getSQLDeclaration($fieldDeclaration, $platform);
        return $sqlDeclaration . " COMMENT '(DC2Type:" . $this->getName() . ")'";
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        /** @var \DateTime $value */
        $value->setTimeZone((self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC')));

        return parent::convertToDatabaseValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $val = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            (self::$utc) ? self::$utc : (self::$utc = new \DateTimeZone('UTC'))
        );

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        $errors = $val->getLastErrors();
        // date was parsed to completely not valid value
        if ($errors['warning_count'] > 0 && (int)$val->format('Y') < 0) {
            return null;
        }

        return $val;
    }
}
