<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing an individual organisation.
 */
class Company extends BaseResource
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
  public $brand;

  /**
   * Company ID, supplied by ClubCollect.
   *
   * @var string
   */
  public $companyId;

  /**
   * @var string
   */
  public $currency;

  /**
   * @var string
   */
  public $email;

  /**
   * @var string|null
   */
  public $houseNumber;

  /**
   * @var string
   */
  public $locale;

  /**
   * @var string
   */
  public $name;

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
      $this->brand       = Cast::toOptString($response['brand']);
      $this->companyId   = Cast::toManString($response['company_id']);
      $this->currency    = Cast::toManString($response['currency']);
      $this->email       = Cast::toManString($response['email']);
      $this->houseNumber = Cast::toOptString($response['house_number']);
      $this->locale      = Cast::toManString($response['locale']);
      $this->name        = Cast::toManString($response['name']);
      $this->zipCode     = Cast::toOptString($response['zipcode']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a company');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
