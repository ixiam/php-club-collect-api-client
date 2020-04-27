<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Resource;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * An entity representing an import, which is a collection of Invoices.
 */
class Import extends BaseResource
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @var string
   */
  public $companyId;

  /**
   * @var string
   */
  public $importId;

  /**
   * The invoice numbers of the invoices that imported under this Import. When this Import is retrieved with
   * ImportEndpoint::get() the value will an array which might be empty. However, when this import is retrieved with
   * ImportEndpoint::page() the value will be null even when there are invoices under the import.
   *
   * @var string[]|null
   */
  public $invoiceIds;

  /**
   * @var string
   */
  public $title;

  /**
   * @var bool
   */
  public $transmitted;

  /**
   * @var \DateTime
   */
  public $transmittedAt;

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
      $this->companyId     = Cast::toManString($response['company_id']);
      $this->importId      = Cast::toManString($response['import_id']);
      $this->title         = Cast::toManString($response['title']);
      $this->transmitted   = Cast::toManBool($response['transmitted']);
      $this->transmittedAt = Cast::toOptDateTime($response['transmitted_at']);

      if (is_array($response['invoice_ids'] ?? null))
      {
        $this->invoiceIds = [];
        foreach ($response['invoice_ids'] as $id)
        {
          $this->invoiceIds[] = Cast::toManString($id);
        }
      }
      else
      {
        $this->invoiceIds = null;
      }
    }
    catch (\Throwable $exception)
    {
      throw new ClubCollectApiException([$exception], 'Failed to create an import');
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns true if the import has been transmitted. Otherwise, returns false.
   *
   * @return bool
   */
  public function isTransmitted(): bool
  {
    return $this->transmitted;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
