<?php

/** @var array<string, mixed> $documentType */
$documentType = json_decode((string) env('DOCUMENT_TYPE'), true);

/** @var array<string, mixed> $customerType */
$customerType = json_decode((string) env('CUSTOMER_TYPE'), true);

/** @var array<string, mixed> $institutionType */
$institutionType = json_decode((string) env('INSTITUTION_TYPE'), true);

return [
    'document-type' => $documentType['document-type'],
    'customer-type' => $customerType['customer-type'],
    'institution-type' => $institutionType['institution-type'],
];
