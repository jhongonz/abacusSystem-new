<?php

$documentType = json_decode(env('DOCUMENT_TYPE'), true);
$customerType = json_decode(env('CUSTOMER_TYPE'), true);
$institutionType = json_decode(env('INSTITUTION_TYPE'), true);

return [
    'document-type' => $documentType['document-type'],
    'customer-type' => $customerType['customer-type'],
    'institution-type' => $institutionType['institution-type'],
    'images' => [
        'imagePathTmp' => '/images/tmp/',
        'imagePathFull' => '/images/full/',
        'imagePathSmall' => '/images/small/',
    ],
];
