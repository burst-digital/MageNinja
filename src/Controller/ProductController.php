<?php

namespace Drupal\mage_ninja\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\mage_ninja\Api\Api;
use Drupal\mage_ninja\Api\JsonExceptionResponse;
use Drupal\mage_ninja\Api\SearchCriteriaBuilder;
use Drupal\mage_ninja\Entity\MageNinjaProduct;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Serializer\Serializer;

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
      /** @var \GuzzleHttp\Client $client */
      $client = Api::getClient();

      /** @var string $token */
      $token = Api::getAdminToken();

      /** @var array $authHeader */
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/products/id/' . $id;
      $options = [
        'headers' => $authHeader
      ];

      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->get($endpoint, $options);

      /** @var \Symfony\Component\Serializer\SerializerInterface $serializer */
      $serializer = \Drupal::service('serializer');

      /** @var MageNinjaProduct $product */
      $product = $serializer->deserialize($response->getBody(), MageNinjaProduct::class, 'json');

      return new JsonResponse($product);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Get a subset of products from Magento.
   *
   * @param int $pageSize
   * @param int $currentPage
   *
   * @return \Drupal\mage_ninja\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getByPage($currentPage, $pageSize) {
    try {
      /** @var \GuzzleHttp\Client $client */
      $client = Api::getClient();

      /** @var string $token */
      $token = Api::getAdminToken();

      /** @var array $authHeader */
      $authHeader = Api::getAuthHeader($token);

      /** @var SearchCriteriaBuilder $searchCriteria */
      $searchCriteria = new SearchCriteriaBuilder();
      $searchCriteria
        ->add(['[pageSize]' => $pageSize])
        ->add(['[currentPage]' => $currentPage]);

      $endpoint = 'V1/products' . $searchCriteria;
      $options = [
        'headers' => $authHeader
      ];

      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->get($endpoint, $options);

      /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
      $decoder = \Drupal::service('serializer');

      /** @var array $products */
      $products = $decoder->decode($response->getBody(), 'json');

      return new JsonResponse($products);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Import all Magento products into Drupal.
   *
   * @return JsonExceptionResponse|RedirectResponse
   */
  public function import() {
    try {
      /** @var array $productCount */
      $productIds = json_decode($this->getAllIds()->getContent());
      // TODO: Check for errors like 401 Unauthorized (instead of "importing" product with ID of 401)

      /** @var int $processedProductsCount */
      $processedProductsCount = 0;

      /** @var int $createdProductsCount */
      $createdProductsCount = 0;

      /** @var int $deletedProductsCount */
      $deletedProductsCount = 0; // TODO: implement deleting products that exist in Drupal but not in Magento

      foreach($productIds as $productId) {
        $processedProductsCount++;

        /** @var MageNinjaProduct $productEntity */
        $productEntity = \Drupal::entityQuery('mage_ninja_product')
          ->condition('reference_id', $productId)
          ->execute();

        if(empty($productEntity)) {
          MageNinjaProduct::create([
            'reference_id' => $productId
          ])->save();

          $createdProductsCount++;
        }
      }

      \Drupal::logger('mage_ninja')->debug('Import: ' . $processedProductsCount . ' products processed.');
      \Drupal::logger('mage_ninja')->info('Import: ' . $createdProductsCount . ' products imported.');
      \Drupal::logger('mage_ninja')->notice('Import: ' . $deletedProductsCount . ' products deleted.');

//      TODO: Don't import in one big request, but in batches
//      TODO: Use batches instead of Queue (queue is for cron)
//      /** @var \Drupal\Core\Queue\QueueInterface $queue */
//      $queue = \Drupal::queue('mage_ninja_product_import', TRUE);
//
//      $queue->createItem($productIds);

      return new RedirectResponse('/');
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Create a new MageNinjaProduct entity, if one does not exist with
   * the provided reference_id.
   *
   * @param int $id
   *  The reference id (Magento product ID)
   *
   * @return JsonExceptionResponse|RedirectResponse
   */
  public function importById($id) {
    try {
      /** @var \Drupal\Core\Entity\Query\QueryInterface $productEntity */
      $productEntity = \Drupal::entityQuery('mage_ninja_product')
        ->condition('reference_id', $id)
        ->execute();

      if (empty($productEntity)) {
        /** @var MageNinjaProduct $productEntity */
        $productEntity = MageNinjaProduct::create([
          'reference_id' => $id
        ]);
        $productEntity->save();
      }

      \Drupal::logger('mage_ninja')->info('Import: product with ID: \'' . $id . '\' imported.');

      return new RedirectResponse('/');
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Get all product Ids from Magento
   *
   * @return \Drupal\mage_ninja\Api\JsonExceptionResponse|\Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getAllIds() {
    try {
      /** @var \GuzzleHttp\Client $client */
      $client = Api::getClient();

      /** @var string $token */
      $token = Api::getAdminToken();

      /** @var array $authHeader */
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/catalog/products';
      $options = [
        'headers' => $authHeader
      ];

      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->get($endpoint, $options);
      $productIds = json_decode($response->getBody());

      return new JsonResponse($productIds);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}