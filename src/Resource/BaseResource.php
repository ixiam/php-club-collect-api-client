<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;

/**
 * Abstract parent class for ClubCollect entities.
 */
abstract class BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ClubCollect API client.
   *
   * @var ClubCollectApiClient
   */
  private $client;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param ClubCollectApiClient $client The ClubCollect API client.
   */
  public function __construct(ClubCollectApiClient $client)
  {
    $this->client = $client;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Converts to array.
   *
   * @return array
   */
  public function toArray() {
    $array = json_decode(json_encode($this), TRUE);
    if (isset($array['client'])) {
      unset($array['client']);
    }
    return $array;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
