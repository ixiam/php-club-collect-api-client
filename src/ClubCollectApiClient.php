<?php
declare(strict_types=1);

namespace SetBased\ClubCollect;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Plaisio\Helper\Url;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SetBased\ClubCollect\Endpoint\CompanyEndpoint;
use SetBased\ClubCollect\Endpoint\ImportEndpoint;
use SetBased\ClubCollect\Endpoint\InvoiceEndpoint;
use SetBased\ClubCollect\Endpoint\TicketEndpoint;
use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;

/**
 * A ClubCollect API client.
 */
class ClubCollectApiClient
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * GET method.
   */
  const HTTP_GET = "GET";

  /**
   * POST method.
   */
  const HTTP_POST = "POST";

  /**
   * DELETE method
   */
  const HTTP_DELETE = "DELETE";

  /**
   * PUT method
   */
  const HTTP_PUT = "PUT";

  /**
   * HTTP status code no content.
   */
  const HTTP_NO_CONTENT = 204;

  /**
   * Version of the remote API.
   */
  const API_VERSION = "v2";

  /**
   * Default response timeout (in seconds).
   */
  const TIMEOUT = 10;

  /**
   * RESTFul company endpoint.
   *
   * @var CompanyEndpoint
   */
  public $company;

  /**
   * RESTFul company endpoint.
   *
   * @var ImportEndpoint
   */
  public $import;

  /**
   * RESTFul invoice endpoint.
   *
   * @var InvoiceEndpoint
   */
  public $invoice;

  /**
   * RESTFul ticket endpoint.
   *
   * @var TicketEndpoint
   */
  public $ticket;

  /**
   * Endpoint of the remote API.
   *
   * @var string
   */
  protected $apiEndpoint;

  /**
   * Partner API Key.
   *
   * @var string
   */
  private $apiKey;

  /**
   * Company ID, supplied by ClubCollect.
   *
   * @var string
   */
  private $companyId;

  /**
   * The http client.
   *
   * @var ClientInterface
   */
  private $httpClient;

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param string $apiEndpoint The API endpoint. For testing 'https://sandbox.clubcollect.com/api', for production
   *                            'https://api.clubcollect.com/api'.
   * @param string $apiKey      Partner API Key.
   * @param string $companyId   Company ID, supplied by ClubCollect.
   *
   * @throws ClubCollectApiException
   */
  public function __construct(string $apiEndpoint, string $apiKey, string $companyId)
  {
    $this->apiEndpoint = $apiEndpoint;
    $this->apiKey      = $apiKey;
    $this->companyId   = $companyId;

    $this->validateIdAndKey($apiKey, $companyId);
    $this->initHttpClient();
    $this->initializeEndpoints();
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the Partner API Key.
   *
   * @return string
   */
  public function getApiKey(): string
  {
    return $this->apiKey;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the Company ID, supplied by ClubCollect.
   *
   * @return string
   */
  public function getCompanyId(): string
  {
    return $this->companyId;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Performs an HTTP call. This method is used by the resource specific classes.
   *
   * @param string     $httpMethod The HTTP method.
   * @param array      $path       The parts of the path.
   * @param array|null $query      The query parameters. A map from key to value.
   * @param array|null $body       The body parameters. A map from key to value.
   *
   * @return array|null
   *
   * @throws ClubCollectApiException
   */
  public function performHttpCall(string $httpMethod,
                                  array $path,
                                  ?array $query = null,
                                  ?array $body = null): ?array
  {
    $url = sprintf('%s/%s%s%s',
                   $this->apiEndpoint,
                   self::API_VERSION,
                   $this->composePath($path),
                   $this->composerQuery($query));

    return $this->performHttpCallToFullUrl($httpMethod, $url, $this->composeRequestBody($body));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Perform an http call to a full url. This method is used by the resource specific classes.
   *
   * @param string                               $httpMethod
   * @param string                               $url
   * @param string|null|resource|StreamInterface $httpBody
   *
   * @return array|null
   *
   * @throws ClubCollectApiException
   */
  public function performHttpCallToFullUrl(string $httpMethod, string $url, ?string $httpBody = null): ?array
  {
    $headers = ['Accept'       => 'application/json',
                'Content-Type' => 'application/json'];
    $request = new Request($httpMethod, $url, $headers, $httpBody);

    try
    {
      $response = $this->httpClient->send($request, ['http_errors' => false]);
    }
    catch (GuzzleException $exception)
    {
      throw ClubCollectApiException::createFromGuzzleException($exception);
    }

    if (!$response)
    {
      throw new ClubCollectApiException("Did not receive API response.");
    }

    return $this->parseResponseBody($response);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the URL for ClubCollect Treasurer SSO login.
   *
   * @param string $salt The salt (provided by ClubCollect).
   * @param string $key  The key (provided by ClubCollect).
   *
   * @return string
   */
  public function ssoUrlTreasurer(string $salt, string $key)
  {
    $hash = hash('sha256', sprintf('%s%s%s%s', date('Ymd'), $this->getCompanyId(), $salt, $key));

    $parts          = parse_url($this->apiEndpoint);
    $parts['path']  = '/treasurer/sso';
    $parts['query'] = http_build_query(['company_uuid' => $this->companyId,
                                        'signature'    => $hash]);
    unset($parts['fragment']);

    return Url::unParseUrl($parts);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the request body.
   *
   * @param array $body The body parameters. A map from key to value.
   *
   * @return string|null
   *
   * @throws ClubCollectApiException
   */
  protected function composeRequestBody(?array $body): ?string
  {
    if (empty($body))
    {
      return null;
    }

    try
    {
      $encoded = \GuzzleHttp\json_encode($body);
    }
    catch (\InvalidArgumentException $exception)
    {
      throw new ClubCollectApiException([$exception],
                                        'Error encoding parameters into JSON: %s',
                                        $exception->getMessage());
    }

    return $encoded;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the path part of the URL.
   *
   * @param array $path The parts of the path.
   *
   * @return string
   */
  private function composePath(array $path): string
  {
    $uri = '';
    foreach ($path as $part)
    {
      if ($part!==null)
      {
        $uri .= '/';
        $uri .= urlencode(Cast::toManString($part));
      }
    }

    return $uri;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the query part of the URL.
   *
   * @param array|null $query The query parameters. A map from key to value.
   *
   * @return string
   */
  private function composerQuery(?array $query): string
  {
    if (empty($query)) return '';

    return '?'.http_build_query($query);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Initializes the http client.
   */
  private function initHttpClient(): void
  {
    $this->httpClient = new Client([RequestOptions::VERIFY  => CaBundle::getBundledCaBundlePath(),
                                    RequestOptions::TIMEOUT => self::TIMEOUT]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Initializes the endpoints.
   */
  private function initializeEndpoints(): void
  {
    $this->company = new CompanyEndpoint($this);
    $this->import  = new ImportEndpoint($this);
    $this->invoice = new InvoiceEndpoint($this);
    $this->ticket  = new TicketEndpoint($this);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Parse the PSR-7 Response body
   *
   * @param ResponseInterface $response
   *
   * @return array|null
   * @throws ClubCollectApiException
   */
  private function parseResponseBody(ResponseInterface $response): ?array
  {
    $body = (string)$response->getBody();
    if (empty($body))
    {
      if ($response->getStatusCode()===self::HTTP_NO_CONTENT)
      {
        return null;
      }

      throw new ClubCollectApiException("No response body found.");
    }

    $object = @json_decode($body, true);
    if (json_last_error()!==JSON_ERROR_NONE)
    {
      throw new ClubCollectApiException("Unable to decode ClubCollect response: '{$body}'.");
    }

    if ($response->getStatusCode()>=400)
    {
      throw ClubCollectApiException::createFromResponse($response);
    }

    return $object;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Validates the Partner API Key and Company ID.
   *
   * @param string $apiKey    Partner API Key.
   * @param string $companyId Company ID, supplied by ClubCollect.
   *
   * @throws ClubCollectApiException
   */
  private function validateIdAndKey(string $apiKey, string $companyId): void
  {
    if (preg_match('/^[0-9a-f]{40,}$/', $apiKey)!=1)
    {
      throw new ClubCollectApiException("Invalid API key: '%s'", $apiKey);
    }

    if (preg_match('/^[0-9a-f]{40,}$/', $companyId)!=1)
    {
      throw new ClubCollectApiException("Invalid company ID: '%s'", $companyId);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
