<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing the name of a customer.
 */
class CustomerName extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var string|null
   */
  public $firstName;

  /**
   * @var string|null
   */
  public $infix;

  /**
   * @var string
   */
  public $lastName;

  /**
   * @var string|null
   */
  public $organization;

  /**
   * @var string|null
   */
  public $prefix;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param ClubCollectApiClient $client   The API client.
   * @param array                $response The API response.
   *
   * @throws ClubCollectApiException
   */
  public function __construct(ClubCollectApiClient $client, array $response)
  {
    parent::__construct($client);

    try
    {
      $this->firstName    = Cast::toOptString($response['first_name']);
      $this->infix        = Cast::toOptString($response['infix']);
      $this->lastName     = Cast::toManString($response['last_name']);
      $this->organization = Cast::toOptString($response['organization']);
      $this->prefix       = Cast::toOptString($response['prefix']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a customer name');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
