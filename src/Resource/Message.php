<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing a message.
 */
class Message extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var \DateTime
   */
  public $date;

  /**
   * @var string
   */
  public $description;

  /**
   * @var string
   */
  public $messageId;

  /**
   * @var string
   */
  public $type;

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
      $this->date        = Cast::toManDateTime($response['date']);
      $this->description = Cast::toManString($response['description']);
      $this->messageId   = Cast::toManString($response['message_id']);
      $this->type        = Cast::toManString($response['type']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a message');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
