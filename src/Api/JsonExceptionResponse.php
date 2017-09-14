<?php

namespace Drupal\hmc\Api;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonExceptionResponse extends JsonResponse {

  /**
   * JsonException constructor.
   * Parses the Exception into a JSON Response.
   *
   * @param Exception $exception
   */
  public function __construct($exception) {
    $data = [
      'code' => $exception->getCode(),
      'message' => $exception->getMessage(),
      'trace' => $exception->getTraceAsString(),
    ];

    parent::__construct($data, $exception->getCode());
  }
}