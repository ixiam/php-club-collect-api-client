<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Endpoint;

use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Resource\BaseResource;
use SetBased\ClubCollect\Resource\Import;

/**
 * Endpoint for imports.
 */
class ImportEndpoint extends Endpoint
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an import.
   *
   * @param string|null $title                The title of the import.
   * @param int|null    $expectedInvoiceCount Number of invoices expected to be added to this import. If provided the
   *                                          Import cannot be transmitted from the ClubCollect User Interface until
   *                                          all invoices are created.
   *
   * @return Import
   *
   * @throws ClubCollectApiException
   */
  public function create(?string $title = null, ?int $expectedInvoiceCount = null): Import
  {
    /** @var Import $resource */
    $resource = parent::restPost(['imports'],
                                 ['api_key' => $this->client->getApiKey()],
                                 ['title'                   => $title,
                                  'company_id'              => $this->client->getCompanyId(),
                                  'expected_invoices_count' => $expectedInvoiceCount]);
    if (!is_a($resource, Import::class))
    {
      throw new ClubCollectApiException('Expected an Import object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Deletes an import from ClubCollect.
   *
   * @param string $id The ID (supplied by ClubCollect) of the import.
   *
   * @throws ClubCollectApiException
   */
  public function delete(string $id): void
  {
    parent::restDelete(['imports', $id], ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Fetches an import from ClubCollect.
   *
   * @param string $importId The ID (supplied by ClubCollect) of the import.
   *
   * @return Import
   *
   * @throws ClubCollectApiException
   */
  public function fetch(string $importId): Import
  {
    /** @var Import $resource */
    $resource = parent::restRead(['imports', $importId], ['api_key' => $this->client->getApiKey()]);
    if (!is_a($resource, Import::class))
    {
      throw new ClubCollectApiException('Expected an Import object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Fetches a list of imports.
   *
   * @param int|null $from The first fetchAll.
   * @param int|null $to   The last fetchAll.
   *
   * @return Import[]
   *
   * @throws ClubCollectApiException
   */
  public function fetchAll(?int $from = null, ?int $to = null): array
  {
    return parent::restList($from,
                            $to,
                            ['companies', $this->client->getCompanyId(), 'imports'],
                            ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates an import.
   *
   * @param string   $id                      The ID (supplied by ClubCollect) of the import.
   * @param int|null $expectedInvoiceCount    Number of invoices expected to be added to this import. If provided the
   *                                          Import cannot be transmitted from the ClubCollect User Interface until
   *                                          all invoices are created.
   *
   * @return Import
   *
   * @throws ClubCollectApiException
   */
  public function update(string $id, ?int $expectedInvoiceCount = null): Import
  {
    /** @var Import $resource */
    $resource = parent::restPut(['imports', $id],
                                ['api_key' => $this->client->getApiKey()],
                                ['expected_invoices_count' => $expectedInvoiceCount]);
    if (!is_a($resource, Import::class))
    {
      throw new ClubCollectApiException('Expected an Import object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an instance of this class.
   *
   * @param array $response The API response.
   *
   * @return Import
   *
   * @throws ClubCollectApiException
   */
  protected function getResourceObject(array $response): BaseResource
  {
    return new Import($this->client, $response);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
