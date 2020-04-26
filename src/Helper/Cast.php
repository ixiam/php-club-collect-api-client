<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Helper;

use SetBased\Helper\InvalidCastException;

/**
 * @inheritDoc
 */
class Cast extends \SetBased\Helper\Cast
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts a value to a string. If the value can not be safely casted to a string throws an exception.
   *
   * @param mixed       $value   The value.
   * @param string|null $default The default value. If the value is null and the default is not null the default value
   *                             will be returned.
   *
   * @return string
   *
   * @throws InvalidCastException
   */
  public static function toManString($value, ?string $default = null): string
  {
    $ret = parent::toManString($value, $default);

    if ($ret==='')
    {
      throw new InvalidCastException('Value can not be converted to string');
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts a value to a DateTime. If the value can not be safely casted to a DateTime throws an exception.
   *
   * @param mixed       $value   The value.
   * @param string|null $default The default value. If the value is null the default value will be returned.
   *
   * @return \DateTime|null
   *
   * @throws \Exception
   */
  public static function toOptDateTime($value, ?string $default = null): ?\DateTime
  {
    $epoch = static::toOptString($value, $default);

    if ($epoch===null) return null;

    return new \DateTime($epoch);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts a value to a string. If the value can not be safely casted to a string throws an exception.
   *
   * @param mixed       $value   The value.
   * @param string|null $default The default value. If the value is null the default value will be returned.
   *
   * @return string|null
   */
  public static function toOptString($value, ?string $default = null): ?string
  {
    if ($value==='') return null;

    return parent::toOptString($value, $default);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
