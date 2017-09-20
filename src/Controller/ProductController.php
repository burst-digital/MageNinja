<?php

namespace Drupal\hmc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\hmc\Api\Api;
use Drupal\hmc\Api\JsonExceptionResponse;
use Drupal\hmc\Api\SearchCriteriaBuilder;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
      $client = Api::getClient();
      $token = Api::getAdminToken();
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/products/id/' . $id;
      $options = [
        'headers' => $authHeader
      ];

      $response = $client->get($endpoint, $options);
      $product = json_decode($response->getBody());

      return new JsonResponse(['product' => $product]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  public function getAll() {
    try {
      $client = Api::getClient();
      $token = Api::getAdminToken();
      $authHeader = Api::getAuthHeader($token);

      $searchCriteria = new SearchCriteriaBuilder();
      $searchCriteria
        ->add(['[pageSize]' => '10'])
        ->add(['[currentPage]' => '1']);

      $endpoint = 'V1/products?' . $searchCriteria;
      $options = [
        'headers' => $authHeader
      ];

      $response = $client->get($endpoint, $options);
      $products = json_decode($response->getBody());

      return new JsonResponse(['products' => $products]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  public function import() {
    try {
      $count = json_decode($this->count()->getContent())->count;



      return $this->getAll();

//      return new RedirectResponse('/');
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * @return \Drupal\hmc\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
   */
  public function count() {
    try {
      $client = Api::getClient();
      $token = Api::getAdminToken();
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/catalog/count';
      $options = [
        'headers' => $authHeader
      ];

      $response = $client->get($endpoint, $options);
      $count = json_decode($response->getBody());

      return new JsonResponse(['count' => $count]);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}