<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Test;

use PHPUnit\Framework\TestCase;
use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Resource\Import;

/**
 * Test cases for Resource Import.
 */
class ImportEndpointTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test CRUD an Import.
   *
   * @throws \Exception
   */
  public function testCrud(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $title = bin2hex(random_bytes(16));
    $api   = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);

    // Create.
    $import1 = $api->import->create($title);
    self::assertSame(Import::class, get_class($import1));
    self::assertFalse($import1->isTransmitted());

    // Read.
    $import2 = $api->import->fetch($import1->importId);
    self::assertEquals($import1, $import2);

    // Update.
    $import3 = $api->import->update($import1->importId, 123);
    self::assertEquals(Import::class, get_class($import3));

    // Delete
    $api->import->delete($import1->importId);

    $this->expectException(ClubCollectApiException::class);
    $api->import->fetch($import1->importId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test transmitting an Import.
   *
   * @throws \Exception
   */
  public function testTransmit(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $title = bin2hex(random_bytes(16));
    $api   = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);

    // Create.
    $import1 = $api->import->create($title, 1);
    self::assertFalse($import1->isTransmitted());

    // Add an invoice
    $api->invoice->create($import1->importId,
                          '123456',
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

    // Transmit.
    $import2 = $api->import->transmit($import1->importId);
    self::assertTrue($import2->isTransmitted());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
