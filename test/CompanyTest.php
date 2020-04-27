<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Test;

use PHPUnit\Framework\TestCase;
use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Resource\Company;

/**
 * Test cases for Resource Company.
 */
class CompanyTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get a company.
   *
   * @throws \Exception
   */
  public function testGet(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $api     = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId);
    $company = $api->company->get();

    self::assertSame(Company::class, get_class($company));
    self::assertSame($companyId, $company->companyId);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get a company with wrong key.
   *
   * @throws \Exception
   */
  public function testGetInvalid(): void
  {
    $apiKey    = trim(file_get_contents(__DIR__.'/../api-key.txt'));
    $companyId = trim(file_get_contents(__DIR__.'/../company-id.txt'));

    $this->expectException(ClubCollectApiException::class);
    $api = new ClubCollectApiClient('https://sandbox.clubcollect.com/api', $apiKey, $companyId.'a');
    $api->company->get();
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
