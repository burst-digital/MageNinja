<?php

namespace Drupal\hmc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hmc\Api\Authentication;
use Drupal\hmc\Api\JsonExceptionResponse;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends ControllerBase {
  /**
   * Returns the product from the Magento API.
   *
   * @param int $id
   *  The id of the product in Magento.
   *
   * @return JsonResponse
   */
  public function getById($id) {
    try {
      $client = Authentication::getClient();
      $token = Authentication::getAdminToken();
      $authHeader = Authentication::getHeader($token);

      $endpoint = 'V1/products/id/' . $id;
      $options = [
        'headers' => $authHeader + [
        ]
      ];

      $response = $client->get($endpoint, $options);
      $product = json_decode($response->getBody());

      return new JsonResponse(['product' => $product]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}