<?php

namespace Drupal\mage_ninja\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mage_ninja\Api\JsonExceptionResponse;
use Drupal\mage_ninja\Api\Api;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends ControllerBase {

  /**
   * Requests a customer token from the Magento API.
   *
   * @param string $username
   *  The customer's username.
   * @param string $password
   *  The customer's password.
   *
   * @return JsonResponse
   */
  public function getCustomerToken($username, $password) {
    try {
      /** @var string $token */
      $token = Api::getCustomerToken($username, $password);

      return new JsonResponse(['token' => $token]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Requests an admin token from the Magento API.
   *
   * @return JsonExceptionResponse|JsonResponse
   */
  public function getAdminToken() {
    try {
      /** @var string $token */
      $token = Api::getAdminToken();

      return new JsonResponse(['token' => $token]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}