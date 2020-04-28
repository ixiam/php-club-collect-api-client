<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Endpoint;

use SetBased\ClubCollect\ClubCollectApiClient;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Resource\BaseResource;

/**
 * Abstract parent class for all end points.
 */
abstract class Endpoint
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The ClubCollect API client.
   *
   * @var ClubCollectApiClient
   */
  protected $client;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param ClubCollectApiClient $client The ClubCollect API client.
   */
  public function __construct(ClubCollectApiClient $client)
  {
    $this->client = $client;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
   *
   * @param array $response The response from the API.
   *
   * @return BaseResource
   */
  abstract protected function createResourceObject(array $response);

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Sends a DELETE request for a single object to the REST API.
   *
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   *
   * @throws ClubCollectApiException
   */
  protected function restDelete(array $path, ?array $query = null): void
  {
    $this->client->performHttpCall(ClubCollectApiClient::HTTP_DELETE, $path, $query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves a single object from the REST API.
   *
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   *
   * @return BaseResource
   *
   * @throws ClubCollectApiException
   */
  protected function restGet(array $path, ?array $query = null): BaseResource
  {
    $result = $this->client->performHttpCall(ClubCollectApiClient::HTTP_GET, $path, $query);
    if ($result===null)
    {
      throw new ClubCollectApiException('Null response received from ClubCollect');
    }

    return $this->createResourceObject($result);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves a list of objects from the REST API.
   *
   * @param string     $key   The key of the objects in the response.
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   *
   * @return array
   *
   * @throws ClubCollectApiException
   */
  protected function restGetList(string $key, array $path, ?array $query = null): array
  {
    $list   = [];
    $result = $this->client->performHttpCall(ClubCollectApiClient::HTTP_GET, $path, $query);
    if ($result===null)
    {
      throw new ClubCollectApiException('Null response received from ClubCollect');
    }

    foreach ($result[$key] as $import)
    {
      $list[] = $this->createResourceObject($import);
    }

    return $list;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves a list of objects from the REST API.
   *
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   *
   * @return array
   *
   * @throws ClubCollectApiException
   */
  protected function restGetPageInfo(array $path, ?array $query = null): array
  {
    $query['page_number'] = 1;
    $result               = $this->client->performHttpCall(ClubCollectApiClient::HTTP_GET, $path, $query);
    if ($result===null)
    {
      throw new ClubCollectApiException('Null response received from ClubCollect');
    }

    $info = $result['page'];
    unset($info['page_number']);

    return $info;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves a list of objects from the REST API.
   *
   * @param string     $key   The key of the objects in the response.
   * @param int|null   $from  The first page.
   * @param int|null   $to    The last page.
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   *
   * @return array
   *
   * @throws ClubCollectApiException
   */
  protected function restGetPages(string $key, ?int $from, ?int $to, array $path, ?array $query = null): array
  {
    $list = [];
    $page = $from ?? 0;
    do
    {
      ++$page;

      $query['page_number'] = $page;
      $result               = $this->client->performHttpCall(ClubCollectApiClient::HTTP_GET, $path, $query);
      if ($result===null)
      {
        throw new ClubCollectApiException('Null response received from ClubCollect');
      }

      foreach ($result[$key] as $import)
      {
        $list[] = $this->createResourceObject($import);
      }
    } while ($page<($to ?? $result['page']['total_pages']));

    return $list;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates or updates a single object from the REST API.
   *
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   * @param array|null $body  The body parameters. A map from key to value.
   *
   * @return BaseResource|null
   *
   * @throws ClubCollectApiException
   */
  protected function restPost(array $path, ?array $query = null, ?array $body = null): ?BaseResource
  {
    $result = $this->client->performHttpCall(ClubCollectApiClient::HTTP_POST, $path, $query, $body);

    if ($result===null) return null;

    return $this->createResourceObject($result);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates a single object on the REST API.
   *
   * @param array      $path  The parts of the path.
   * @param array|null $query The query parameters. A map from key to value.
   * @param array|null $body  The body parameters. A map from key to value.
   *
   * @return BaseResource The update resource.
   *
   * @throws ClubCollectApiException
   */
  protected function restPut(array $path, ?array $query = null, ?array $body = null): BaseResource
  {
    $result = $this->client->performHttpCall(ClubCollectApiClient::HTTP_PUT, $path, $query, $body);
    if ($result===null)
    {
      throw new ClubCollectApiException('Null response received from ClubCollect');
    }

    return $this->createResourceObject($result);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
