<?php declare(strict_types=1);

namespace Simlux\Curl\Schema;

use Carbon\Carbon;
use Simlux\Curl\Exceptions\WrongRangeException;
use Simlux\String\StringBuffer;

/**
 * Class NamingSchema
 *
 * @package Simlux\Curl\Schema
 */
class NamingSchema
{
    const COMPONENT_INCREMENT            = '#increment#';
    const COMPONENT_INCREMENT_ZEROFILLED = '#increment_zerofilled#';
    const COMPONENT_DATETIME             = '#datetime#';

    /**
     * @var string
     */
    private $schema;

    /**
     * @var int
     */
    private $increment = 0;

    /**
     * @var int
     */
    private $incrementWidth;

    /**
     * @var array
     */
    private $components = [];

    /**
     * @var Carbon
     */
    private $dateTime;

    /**
     * @var string
     */
    private $dateTimeFormat = 'Ymd_His';

    /**
     * NamingSchema constructor.
     *
     * @param string $schema
     * @param array  $components
     */
    public function __construct(string $schema, array $components = [])
    {
        $this->schema     = $schema;
        $this->components = $components;
    }

    /**
     * @return string
     */
    private function fillComponents(): string
    {
        $buffer = new StringBuffer($this->schema);

        foreach ($this->components as $name => $value) {
            $buffer->replace($name, $this->getValue($value));
        }

        return $buffer->toString();
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function getValue($value): string
    {
        switch ($value) {
            case self::COMPONENT_INCREMENT:
                $value = (string) $this->increment;
                break;
            case self::COMPONENT_INCREMENT_ZEROFILLED:
                $value = $this->zeroFill($this->increment, $this->incrementWidth);
                break;

            case self::COMPONENT_DATETIME:
                if (is_null($this->dateTime)) {
                    $this->dateTime = Carbon::now();
                }
                $value = $this->dateTime->format($this->dateTimeFormat);
                break;
        }

        return $value;
    }

    /**
     * @param int $integer
     * @param int $width
     *
     * @return string
     * @throws WrongRangeException
     */
    private function zeroFill(int $integer, int $width): string
    {
        if (strlen((string) $integer) > $width) {
            throw new WrongRangeException(sprintf('%d -> %d', $integer, $width));
        }

        return str_pad((string) $integer, $width, '0', STR_PAD_LEFT);
    }

    /**
     * @param int $incrementStart
     */
    public function setIncrementStart(int $incrementStart)
    {
        $this->increment = $incrementStart - 1;
    }

    /**
     * @param int $incrementWidth
     */
    public function setIncrementWidth(int $incrementWidth)
    {
        $this->incrementWidth = $incrementWidth;
    }

    /**
     * @return string
     */
    public function next(): string
    {
        $this->increment++;

        return $this->fillComponents();
    }

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat(string $dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @param Carbon $dateTime
     */
    public function setDateTime(Carbon $dateTime)
    {
        $this->dateTime = $dateTime;
    }
}