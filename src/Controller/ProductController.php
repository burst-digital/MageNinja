<?php

namespace Drupal\hmc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hmc\Api\Authentication;
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
    $client = Authentication::getClient();
    $token = Authentication::getAdminToken();
    $authentication = Authentication::getAuthentication($token);

    $endpoint = 'V1/products/id/';

    $response = $client->post($endpoint . $id, $authentication);
    $product = $response->getBody();

    return new JsonResponse($product);
  }
}