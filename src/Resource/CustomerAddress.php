<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing the address of a customer.
 */
class CustomerAddress extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var string|null
   */
  public $address1;

  /**
   * @var string|null
   */
  public $address2;

  /**
   * @var string|null
   */
  public $city;

  /**
   * @var string|null
   */
  public $countryCode;

  /**
   * @var string|null
   */
  public $houseNumber;

  /**
   * @var string|null
   */
  public $locality;

  /**
   * @var string|null
   */
  public $state;

  /**
   * @var string|null
   */
  public $zipCode;

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
      $this->address1    = Cast::toOptString($response['address1']);
      $this->address2    = Cast::toOptString($response['address2']);
      $this->city        = Cast::toOptString($response['city']);
      $this->countryCode = Cast::toOptString($response['country_code']);
      $this->houseNumber = Cast::toOptString($response['house_number']);
      $this->locality    = Cast::toOptString($response['locality']);
      $this->state       = Cast::toOptString($response['state']);
      $this->zipCode     = Cast::toOptString($response['zipcode']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a customer address');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
