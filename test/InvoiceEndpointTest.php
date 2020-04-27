<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Test;

use PHPUnit\Framework\TestCase;
use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;

/**
 * Test cases for Resource Invoice.
 */
class InvoiceEndpointTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test CRUD an Invoice.
   *
   * @throws \Exception
   */
  public function testCredit(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $title  = bin2hex(random_bytes(16));
    $api    = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
    $import = $api->import->create($title);

    $number = bin2hex(random_bytes(5));

    // Create.
    $invoice1 = $api->invoice->create($import->importId,
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

    // Credit.
//    $invoice2 = $api->invoice->credit($invoice1->invoiceId,
//                                      $number,
//                                      [['amount_cents' => -500,
//                                        'description'  => 'Contribution']],
//                                      5000);
    // self::assertSame(5000, $invoice2->amountTotalCents);
    // XXX Does work yet.
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test credit and retracts an Invoice.
   *
   * @throws \Exception
   */
  public function testCreditAndRetract(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $title  = bin2hex(random_bytes(16));
    $api    = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
    $import = $api->import->create($title);

    $number = bin2hex(random_bytes(5));

    // Create.
    $invoice1 = $api->invoice->create($import->importId,
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
                                      [['amount_cents' => 5000,
                                        'description'  => 'Contribution'],
                                       ['amount_cents' => 500,
                                        'description'  => 'Extra Contribution']],
                                      5500);

    $invoice2 = $api->invoice->creditAndRetract($invoice1->invoiceId,
                                                $number,
                                                'Payed',
                                                'Personal services to treasurer',
                                                false);
    self::assertSame(0, $invoice2->amountTotalCents);
    self::assertCount(3, $invoice2->invoiceLines);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test CRUD an Invoice.
   *
   * @throws \Exception
   */
  public function testCrud(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $title  = bin2hex(random_bytes(16));
    $api    = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
    $import = $api->import->create($title);

    $number = bin2hex(random_bytes(5));

    // Create.
    $invoice1 = $api->invoice->create($import->importId,
                                      $number,
                                      null,
                                      null,
                                      null,
                                      null,
                                      'NL',
                                      ['first_name' => 'John',
                                       'last_name'  => 'Doe'],
                                      ['address1'     => 'Some Street',
                                       'house_number' => '1',
                                       'zip-code'     => '1000',
                                       'city'         => 'A Big One',
                                       'country_code' => 'NL'],
                                      ['email_address' => 'noreply@setbased.nl'],
                                      ['phone_number' => '+31-6-12345678',
                                       'country_code' => 'NL'],
                                      [['amount_cents' => 5000,
                                        'description'  => 'Contribution'],
                                       ['amount_cents' => 500,
                                        'description'  => 'Extra Contribution']],
                                      5500);
    self::assertNotNull($invoice1->invoiceId);

    // Read
    $invoice2 = $api->invoice->fetch($invoice1->invoiceId);
    self::assertEquals($invoice1, $invoice2);

    // Update
    $invoice3 = $api->invoice->update($invoice1->invoiceId,
                                      $number,
                                      'reference',
                                      null,
                                      'asso',
                                      'club',
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
                                       'country_code' => 'BE']);
    self::assertSame('reference', $invoice3->reference);
    self::assertSame('asso', $invoice3->federationMembershipNumber);
    self::assertSame('club', $invoice3->clubMembershipNumber);
    self::assertSame('Jane', $invoice3->customer->name->firstName);
    self::assertSame('Other Street', $invoice3->customer->address->address1);

    // Delete
    $api->invoice->delete($invoice1->invoiceId);

    $this->expectException(ClubCollectApiException::class);
    $api->invoice->fetch($invoice1->invoiceId);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
