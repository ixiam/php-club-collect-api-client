<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Exception;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use SetBased\Exception\FormattedException;

/**
 *
 */
class ClubCollectApiException extends \Exception
{
  //--------------------------------------------------------------------------------------------------------------------
  use FormattedException;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an exception given a Guzzle exception.
   *
   * @param GuzzleException $exception The Guzzle exception.
   *
   * @return static
   *
   * @throws static
   */
  public static function createFromGuzzleException(GuzzleException $exception): self
  {
    if (method_exists($exception, 'hasResponse') && method_exists($exception, 'getResponse'))
    {
      if ($exception->hasResponse())
      {
        return static::createFromResponse($exception->getResponse());
      }
    }

    return new static([$exception->getCode()], $exception->getMessage());
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an exception given the response from the http client.
   *
   * @param ResponseInterface $response The response from the http client.
   *
   * @return static
   */
  public static function createFromResponse(ResponseInterface $response): self
  {
    return new static([$response->getStatusCode()],
                      "Error executing API call, status %d: %s",
                      $response->getStatusCode(),
                      $response->getBody());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
