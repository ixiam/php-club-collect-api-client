<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing a phone number of a customer.
 */
class CustomerPhone extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var string|null
   */
  public $countryCode;

  /**
   * @var string|null
   */
  public $phoneNumber;

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
      $this->countryCode = Cast::toOptString($response['country_code']);
      $this->phoneNumber = Cast::toOptString($response['phone_number']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a customer phone number');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
