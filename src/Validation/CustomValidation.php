<?php

namespace App\Validation;

class CustomValidation
{
  public static function isBase64(string $str): bool
  {
    if (base64_encode(base64_decode($str, true)) === $str)
    {
      return true;
    }
    return false;
  }
}