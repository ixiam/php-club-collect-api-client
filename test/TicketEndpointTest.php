<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Test;

use PHPUnit\Framework\TestCase;
use SetBased\ClubCollect\ClubCollectApiClient;

/**
 * Test cases for Resource Ticket.
 */
class TicketEndpointTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test all methods.
   *
   * @throws \Exception
   */
  public function testTickets(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $title  = bin2hex(random_bytes(16));
    $api    = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
    $import = $api->import->create($title);

    $number = bin2hex(random_bytes(5));

    $invoice = $api->invoice->create($import->importId,
                                     $number,
                                     null,
                                     null,
                                     null,
                                     null,
                                     'NL',
                                     ['first_name' => 'Jane',
                                      'last_name'  => 'Doe'],
                                     ['address1'     => 'Other Street',
                                      'house_number' => '2',
                                      'zip-code'     => '2000',
                                      'city'         => 'A Not So Big One',
                                      'country_code' => 'BE'],
                                     ['email_address' => 'noreply@setbased.nl'],
                                     ['phone_number' => '+32-4-72345678',
                                      'country_code' => 'BE'],
                                     [['amount_cents' => 5500,
                                       'description'  => 'Contribution']],
                                     5500);

    // Create ticket
    $message = 'My name is not Jane';
    $ticket1 = $api->ticket->create($invoice->invoiceId, $message);
    self::assertSame($invoice->invoiceId, $ticket1->invoiceId);
    self::assertSame($message, $message);
    self::assertSame('COMPANY', $ticket1->sender);
    self::assertNotNull($ticket1->ticketId);

    // Fetch tickets
    $tickets1 = $api->ticket->fetch($invoice->invoiceId);
    self::assertCount(1, $tickets1);
    self::assertEquals($ticket1, $tickets1[0]);

    // Assign and archive.
    $api->ticket->assignToSupport($invoice->invoiceId);
    $api->ticket->archive($invoice->invoiceId);

    // Fetch all archived tickets.
    $tickets2 = $api->ticket->fetchAll('archived');
    $key      = null;
    foreach ($tickets2 as $id => $ticket)
    {
      if ($ticket->ticketId===$ticket1->ticketId)
      {
        $key = $id;
      }
    }
    self::assertNotNull($key);
    self::assertSame($invoice->invoiceId, $tickets2[$key]->invoiceId);

    // Fetch page info.
    $info = $api->ticket->fetchPageInfo('archived');
    self::assertArrayHasKey('page_size', $info);
    self::assertArrayHasKey('total_entries', $info);
    self::assertArrayHasKey('total_pages', $info);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
