<?php

namespace NicolasChoquet\nlighten\iTop\Extension\DatacenterViewExtended\Common\classes;

use Exception;

class Response
{
    public function success(array $data = []): void
    {
        echo json_encode(['ok' => true, ...($data === [] ? [] : ['data' => $data])], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    public function error(Exception $error): void
    {
        http_response_code($error->getCode() ?? 500);
        echo json_encode(['ok' => false, 'error' => $error->getMessage()]);
    }
}