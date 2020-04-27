<?php
declare(strict_types=1);

namespace SetBased\ClubCollect\Endpoint;

use SetBased\ClubCollect\Exception\ClubCollectApiException;
use SetBased\ClubCollect\Helper\Cast;
use SetBased\ClubCollect\Resource\BaseResource;
use SetBased\ClubCollect\Resource\Invoice;

/**
 * Endpoint for companies.
 */
class InvoiceEndpoint extends Endpoint
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Creates an invoice.
   *
   * @param string      $importId         ID of Import to which the Invoice should belong.
   * @param string      $externalInvoiceNumber
   * @param string|null $reference
   * @param string|null $directDebitIban  When supplied, will be accepted and added to the Invoice only if it is a
   *                                      valid IBAN.
   * @param string|null $federationMembershipNumber
   * @param string|null $clubMembershipNumber
   * @param string|null $locale           When supplied, the invoice's locale will be set to this value. From { de en
   *                                      fr it nl }
   * @param array       $customerName
   * @param array       $customerAddress
   * @param array       $customerEmail
   * @param array       $customerPhone
   * @param array       $invoiceLines
   * @param int         $amountTotalCents Must be equal to the sum of the amounts of the Invoice Lines. May be zero or
   *                                      negative.
   *
   * @return Invoice
   *
   * @throws ClubCollectApiException
   */
  public function create(string $importId,
                         string $externalInvoiceNumber,
                         ?string $reference,
                         ?string $directDebitIban,
                         ?string $federationMembershipNumber,
                         ?string $clubMembershipNumber,
                         ?string $locale,
                         array $customerName,
                         array $customerAddress,
                         array $customerEmail,
                         array $customerPhone,
                         array $invoiceLines,
                         int $amountTotalCents): Invoice
  {
    /** @var Invoice $resource */
    $resource = parent::restPost(
      ['invoices'],
      ['api_key' => $this->client->getApiKey()],
      ['import_id'                    => $importId,
       'external_invoice_number'      => $externalInvoiceNumber,
       'reference'                    => $reference,
       'direct_debit_iban'            => $directDebitIban,
       'federation_membership_number' => Cast::toOptString($federationMembershipNumber),
       'club_membership_number'       => Cast::toOptString($clubMembershipNumber),
       'locale'                       => $locale,
       'customer'                     => ['name'    => ['prefix'       => $customerName['prefix'] ?? null,
                                                        'first_name'   => $customerName['first_name'] ?? null,
                                                        'infix'        => $customerName['infix'] ?? null,
                                                        'last_name'    => $customerName['last_name'] ?? null,
                                                        'organization' => $customerName['organization'] ?? null],
                                          'address' => ['address1'     => $customerAddress['address1'] ?? null,
                                                        'address2'     => $customerAddress['address2'] ?? null,
                                                        'locality'     => $customerAddress['locality'] ?? null,
                                                        'house_number' => Cast::toOptString($customerAddress['house_number'] ?? null),
                                                        'state'        => $customerAddress['state'] ?? null,
                                                        'zipcode'      => $customerAddress['zip_code'] ?? null,
                                                        'city'         => $customerAddress['city'] ?? null,
                                                        'country_code' => $customerAddress['country_code'] ?? null],
                                          'email'   => ['email_address' => $customerEmail['email_address'] ?? null],
                                          'phone'   => ['phone_number' => $customerPhone['phone_number'] ?? null,
                                                        'country_code' => $customerPhone['country_code'] ?? null]],
       'invoice_lines'                => $this->composeInvoiceLines($invoiceLines),
       'amount_total_cents'           => $amountTotalCents]);
    if (!is_a($resource, Invoice::class))
    {
      throw new ClubCollectApiException('Expected an Invoice object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Credits an invoice.
   *
   * @param string  $invoiceId Invoice ID for the Invoice to be credited.
   * @param string  $externalInvoiceNumber
   * @param array[] $invoiceLines
   * @param int     $amountTotalCents
   *
   * @return Invoice
   *
   * @throws ClubCollectApiException
   */
  public function credit(string $invoiceId,
                         string $externalInvoiceNumber,
                         array $invoiceLines,
                         int $amountTotalCents): Invoice
  {
    /** @var Invoice $resource */
    $resource = parent::restPost(['invoices', $invoiceId, 'credit'],
                                 ['api_key' => $this->client->getApiKey()],
                                 ['external_invoice_number' => $externalInvoiceNumber,
                                  'invoice_lines'           => $this->composeInvoiceLines($invoiceLines),
                                  'amount_total_cents'      => $amountTotalCents]);

    if (!is_a($resource, Invoice::class))
    {
      throw new ClubCollectApiException('Expected an Invoice object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Credits and retracts an invoice.
   *
   * @param string      $invoiceId Invoice ID for the Invoice to be credited.
   *
   * @param string      $externalInvoiceNumber
   * @param string      $description
   * @param string|null $retractionReason
   * @param bool|null   $showRetractionReasonToCustomer
   *
   * @return Invoice
   *
   * @throws ClubCollectApiException
   */
  public function creditAndRetract(string $invoiceId,
                                   string $externalInvoiceNumber,
                                   string $description,
                                   ?string $retractionReason,
                                   ?bool $showRetractionReasonToCustomer): Invoice
  {
    /** @var Invoice $resource */
    $resource = parent::restPost(['invoices', $invoiceId, 'credit_and_retract'],
                                 ['api_key' => $this->client->getApiKey()],
                                 ['external_invoice_number'            => $externalInvoiceNumber,
                                  'description'                        => $description,
                                  'retraction_reason'                  => $retractionReason,
                                  'show_retraction_reason_to_customer' => $showRetractionReasonToCustomer]);

    if (!is_a($resource, Invoice::class))
    {
      throw new ClubCollectApiException('Expected an Invoice object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Deletes an invoice from ClubCollect.
   *
   * @param string $invoiceId The invoice ID, supplied by ClubCollect.
   *
   * @throws ClubCollectApiException
   */
  public function delete(string $invoiceId): void
  {
    parent::restDelete(['invoices', $invoiceId],
                       ['api_key' => $this->client->getApiKey()]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Retrieves an invoice from ClubCollect.
   *
   * @param string $invoiceId The invoice ID, supplied by ClubCollect.
   *
   * @return Invoice
   *
   * @throws ClubCollectApiException
   */
  public function get(string $invoiceId): Invoice
  {
    /** @var Invoice $resource */
    $resource = parent::restRead(['invoices', $invoiceId],
                                 ['api_key' => $this->client->getApiKey()]);
    if (!is_a($resource, Invoice::class))
    {
      throw new ClubCollectApiException('Expected an Invoice object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Updates an invoice.
   *
   * @param string      $invoiceId        ID of the invoice, supplied by ClubCollect.
   * @param string      $externalInvoiceNumber
   * @param string|null $reference
   * @param string|null $directDebitIban  When supplied, will be accepted and added to the Invoice only if it is a
   *                                      valid IBAN.
   * @param string|null $federationMembershipNumber
   * @param string|null $clubMembershipNumber
   * @param string|null $locale           When supplied, the invoice's locale will be set to this value. From { de en
   *                                      fr it nl }
   * @param array       $customerName
   * @param array       $customerAddress
   * @param array       $customerEmail
   * @param array       $customerPhone
   *
   * @return Invoice
   *
   * @throws ClubCollectApiException
   */
  public function update(string $invoiceId,
                         string $externalInvoiceNumber,
                         ?string $reference,
                         ?string $directDebitIban,
                         ?string $federationMembershipNumber,
                         ?string $clubMembershipNumber,
                         ?string $locale,
                         array $customerName,
                         array $customerAddress,
                         array $customerEmail,
                         array $customerPhone): Invoice
  {
    /** @var Invoice $resource */
    $resource = parent::restPut(
      ['invoices', $invoiceId],
      ['api_key' => $this->client->getApiKey()],
      ['external_invoice_number'      => $externalInvoiceNumber,
       'reference'                    => $reference,
       'direct_debit_iban'            => $directDebitIban,
       'federation_membership_number' => Cast::toOptString($federationMembershipNumber),
       'club_membership_number'       => Cast::toOptString($clubMembershipNumber),
       'locale'                       => $locale,
       'customer'                     => ['name'    => ['prefix'       => $customerName['prefix'] ?? null,
                                                        'first_name'   => $customerName['first_name'] ?? null,
                                                        'infix'        => $customerName['infix'] ?? null,
                                                        'last_name'    => $customerName['last_name'] ?? null,
                                                        'organization' => $customerName['organization'] ?? null],
                                          'address' => ['address1'     => $customerAddress['address1'] ?? null,
                                                        'address2'     => $customerAddress['address2'] ?? null,
                                                        'locality'     => $customerAddress['locality'] ?? null,
                                                        'house_number' => Cast::toOptString($customerAddress['house_number'] ?? null),
                                                        'state'        => $customerAddress['state'] ?? null,
                                                        'zipcode'      => $customerAddress['zip_code'] ?? null,
                                                        'city'         => $customerAddress['city'] ?? null,
                                                        'country_code' => $customerAddress['country_code'] ?? null],
                                          'email'   => ['email_address' => $customerEmail['email_address'] ?? null],
                                          'phone'   => ['phone_number' => $customerPhone['phone_number'] ?? null,
                                                        'country_code' => $customerPhone['country_code'] ?? null]]]);
    if (!is_a($resource, Invoice::class))
    {
      throw new ClubCollectApiException('Expected an Invoice object, got a %s', get_class($resource));
    }

    return $resource;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns an instance of this class.
   *
   * @param array $response The API response.
   *
   * @return Invoice
   *
   * @throws ClubCollectApiException
   */
  protected function getResourceObject(array $response): BaseResource
  {
    return new Invoice($this->client, $response);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Composes the invoice lines part of the request body.
   *
   * @param array[] $invoiceLines The data of the invoice lines.
   *
   * @return array[]
   */
  private function composeInvoiceLines(array $invoiceLines): array
  {
    $ret = [];

    foreach ($invoiceLines as $invoiceLine)
    {
      $ret[] = ['invoice_line_id' => $invoiceLine['invoice_line_id'] ?? null,
                'type'            => $invoiceLine['type'] ?? null,
                'amount_cents'    => $invoiceLine['amount_cents'],
                'description'     => $invoiceLine['description'],
                'date'            => $invoiceLine['date'] ?? date('c')];
    }

    return $ret;
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
