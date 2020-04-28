<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Endpoint;

use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Resource\BaseResource;
use SetBased\ClubCollect\Resource\Ticket;

/**
 * Endpoint for tickets.
 */
class TicketEndpoint extends Endpoint
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ID of the current invoice for which we are manipulating tickets.
   *
   * @var string|null
   */
  private $invoiceId;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Archives all tickets for an invoice from ClubCollect.
   *
   * @param string $invoiceId The ID of Invoice for the tickets are archived.
   *
   * @throws ClubCollectApiException
   */
  public function archive(string $invoiceId): void
  {
    $this->invoiceId = $invoiceId;

    parent::restPost(['invoices', $invoiceId, 'tickets', 'actions', 'archive'],
                     ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Assigns all tickets for an invoice to the ClubCollect support team.
   *
   * @param string $invoiceId The ID of Invoice for the tickets are assigned.
   *
   * @throws ClubCollectApiException
   */
  public function assignToSupport(string $invoiceId): void
  {
    $this->invoiceId = $invoiceId;

    parent::restPost(['invoices', $invoiceId, 'tickets', 'actions', 'assign_to_support'],
                     ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates a ticket for an invoice from ClubCollect.
   *
   * @param string $invoiceId The ID of Invoice for the ticket is created.
   * @param string $message   The message of the ticket.
   *
   * @return Ticket
   *
   * @throws ClubCollectApiException
   */
  public function create(string $invoiceId, string $message): Ticket
  {
    $this->invoiceId = $invoiceId;

    /** @var Ticket $resource */
    $resource = parent::restPost(['invoices', $invoiceId, 'tickets'],
                                 ['api_key' => $this->client->getApiKey()],
                                 ['message' => $message]);
    if (!is_a($resource, Ticket::class))
    {
      throw new ClubCollectApiException('Expected an Ticket object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Fetches all tickets for an invoice from ClubCollect.
   *
   * @param string $invoiceId The ID of Invoice for which tickets to be fetched.
   *
   * @return Ticket[]
   *
   * @throws ClubCollectApiException
   */
  public function fetch(string $invoiceId): array
  {
    $this->invoiceId = $invoiceId;

    return parent::restGetList('tickets',
                               ['invoices', $invoiceId, 'tickets'],
                               ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the list of tickets linked to a company, filtered by Ticket status, paginated and sorted in ascending
   * order, i.e. from oldest to newest.
   *
   * @param string   $status The status of the ticket. One of
   *                         <ul>
   *                         <li> unanswered
   *                         <li> answered
   *                         <li> archived
   *                         </ul>
   * @param int|null $from   The first to fetch. Defaults to the first page.
   * @param int|null $to     The last to fetch. Defaults to the last page.
   *
   * @return Ticket[]
   *
   * @throws ClubCollectApiException
   */
  public function fetchAll(string $status, ?int $from = null, ?int $to = null): array
  {
    $this->invoiceId = null;

    return parent::restGetPages('tickets',
                                $from,
                                $to,
                                ['companies', $this->client->getCompanyId(), 'tickets', $status],
                                ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Fetches info about available pages.
   *
   * @param string   $status The status of the ticket. One of
   *                         <ul>
   *                         <li> unanswered
   *                         <li> answered
   *                         <li> archived
   *                         </ul>
   *
   * @return array Has the following keys: page_size, total_entries, total_pages.
   *
   * @throws ClubCollectApiException
   */
  public function fetchPageInfo(string $status): array
  {
    return parent::restGetPageInfo(['companies', $this->client->getCompanyId(), 'tickets', $status],
                                   ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an instance of this class.
   *
   * @param array $response The API response.
   *
   * @return Ticket
   *
   * @throws ClubCollectApiException
   */
  protected function createResourceObject(array $response): BaseResource
  {
    return new Ticket($this->client, $response, $this->invoiceId);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
