<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Endpoint;

use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Resource\BaseResource;
use SetBased\ClubCollect\Resource\Company;

/**
 * Endpoint for companies.
 */
class CompanyEndpoint extends Endpoint
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Fetches the company from ClubCollect.
   *
   * @return Company
   *
   * @throws ClubCollectApiException
   */
  public function fetch(): Company
  {
    /** @var Company $resource */
    $resource = parent::restRead(['companies', $this->client->getCompanyId()],
                                 ['api_key' => $this->client->getApiKey()]);
    if (!is_a($resource, Company::class))
    {
      throw new ClubCollectApiException('Expected a Company object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an instance of this class.
   *
   * @param array $response The API response.
   *
   * @return Company
   *
   * @throws ClubCollectApiException
   */
  protected function getResourceObject(array $response): BaseResource
  {
    return new Company($this->client, $response);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
