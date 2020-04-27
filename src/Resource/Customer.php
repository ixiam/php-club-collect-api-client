<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;

/**
 * An entity representing a customer.
 */
class Customer extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var CustomerAddress
   */
  public $address;

  /**
   * @var CustomerEmail
   */
  public $email;

  /**
   * @var CustomerName
   */
  public $name;

  /**
   * @var CustomerPhone
   */
  public $phone;

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
      $this->address = new CustomerAddress($client, $response['address']);
      $this->email   = new CustomerEmail($client, $response['email']);
      $this->name    = new CustomerName($client, $response['name']);
      $this->phone   = new CustomerPhone($client, $response['phone']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a customer email address');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
