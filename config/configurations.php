<?php

$documentType = json_decode(env('DOCUMENT_TYPE'), true);
$maritalType = json_decode(env('MARITAL_TYPE'), true);

return [
    'document-type' => $documentType['document-type'],
    'marital-type' => $maritalType['status-marital-type'],
];
