<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing a ticket.
 */
class Ticket extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var \DateTime
   */
  public $date;

  /**
   * @var string
   */
  public $message;

  /**
   * @var string
   */
  public $sender;

  /**
   * @var string
   */
  public $ticketId;

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
      $this->date     = Cast::toManDateTime($response['date']);
      $this->message  = Cast::toManString($response['message']);
      $this->sender   = Cast::toManString($response['sender']);
      $this->ticketId = Cast::toManString($response['ticket_id']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create a ticket');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
