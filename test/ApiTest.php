<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Test;

use PHPUnit\Framework\TestCase;
use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;

/**
 * Test cases for Resource Company.
 */
class ApiTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test wrong CompanyId.
   */
  public function testInvalidCompanyId(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = 'xxx';

    $this->expectException(ClubCollectApiException::class);
    new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test wrong API-key
   */
  public function testInvalidWrongKey(): void
  {
    $apiKey    = 'xxx';
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $this->expectException(ClubCollectApiException::class);
    new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test wrong API-key
   */
  public function testIncorrectCompanyId(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = '0'.trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $api = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
    self::assertSame(ClubCollectApiClient::class, get_class($api));
    $this->expectException(ClubCollectApiException::class);
    $api->company->fetch();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test wrong endpoint
   */
  public function testIncorrectEndpoint(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = '0'.trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $api = new ClubCollectApiClient('https://sandbox.clubcollect.comx/api', $apiKey, $companyId);
    self::assertSame(ClubCollectApiClient::class, get_class($api));
    $this->expectException(ClubCollectApiException::class);
    $api->company->fetch();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
