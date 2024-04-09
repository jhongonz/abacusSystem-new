<?php

$documentType = json_decode(env('DOCUMENT_TYPE'), true);

return [
    'document-type' => $documentType['document-type'],
];
