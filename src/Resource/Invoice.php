<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing an invoice.
 */
class Invoice extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var int
   */
  public $amountTotalCents;

  /**
   * @var string|null
   */
  public $clubMembershipNumber;

  /**
   * @var Customer
   */
  public $customer;

  /**
   * @var string|null
   */
  public $directDebitIban;

  /**
   * @var string|null
   */
  public $externalInvoiceNumber;

  /**
   * @var string|null
   */
  public $federationMembershipNumber;

  /**
   * Import ID, supplied by ClubCollect.
   *
   * @var string
   */
  public $importId;

  /**
   * Invoice ID, supplied by ClubCollect.
   *
   * @var string
   */
  public $invoiceId;

  /**
   * @var InvoiceLine[]
   */
  public $invoiceLines = [];

  /**
   * @var string
   */
  public $invoiceNumber;

  /**
   * @var string|null
   */
  public $locale;

  /**
   * @var Message[]
   */
  public $messages = [];

  /**
   * @var string|null
   */
  public $reference;

  /**
   * @var \DateTime|null
   */
  public $retractedAt;

  /**
   * @var string|null
   */
  public $retractionReason;

  /**
   * @var bool
   */
  public $showRetractionReasonToCustomer;

  /**
   * @var Ticket[]
   */
  public $tickets = [];

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
      $this->amountTotalCents               = Cast::toManInt($response['amount_total_cents']);
      $this->clubMembershipNumber           = Cast::toOptString($response['club_membership_number']);
      $this->directDebitIban                = Cast::toOptString($response['direct_debit_iban']);
      $this->externalInvoiceNumber          = Cast::toOptString($response['external_invoice_number']);
      $this->federationMembershipNumber     = Cast::toOptString($response['federation_membership_number']);
      $this->importId                       = Cast::toManString($response['import_id']);
      $this->invoiceId                      = Cast::toManString($response['invoice_id']);
      $this->invoiceNumber                  = Cast::toManString($response['invoice_number']);
      $this->locale                         = Cast::toOptString($response['locale']);
      $this->reference                      = Cast::toOptString($response['reference']);
      $this->retractedAt                    = Cast::toOptDateTime($response['retracted_at']);
      $this->retractionReason               = Cast::toOptString($response['retraction_reason']);
      $this->showRetractionReasonToCustomer = Cast::toManBool($response['show_retraction_reason_to_customer']);

      $this->customer = new Customer($client, $response['customer']);

      foreach ($response['invoice_lines'] as $line)
      {
        $this->invoiceLines[] = new InvoiceLine($client, $line);
      }

      foreach ($response['messages'] as $line)
      {
        $this->messages[] = new Message($client, $line);
      }

      foreach ($response['tickets'] as $line)
      {
        $this->tickets[] = new Ticket($client, $line);
      }
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create an invoice');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
