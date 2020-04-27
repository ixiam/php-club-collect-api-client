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
}

//----------------------------------------------------------------------------------------------------------------------
