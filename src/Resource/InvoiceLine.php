<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing a line in an invoice.
 */
class InvoiceLine extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var int
   */
  public $amountCents;

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
  public $invoiceLineId;

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
      $this->amountCents   = Cast::toManInt($response['amount_cents']);
      $this->date          = Cast::toManDateTime($response['date']);
      $this->description   = Cast::toOptString($response['description']);
      $this->invoiceLineId = Cast::toManString($response['invoice_line_id']);
      $this->type          = Cast::toManString($response['type']);
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create an invoice line');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
