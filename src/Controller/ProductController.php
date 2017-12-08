<?php

namespace Drupal\mage_ninja\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\mage_ninja\Api\Api;
use Drupal\mage_ninja\Api\JsonExceptionResponse;
use Drupal\mage_ninja\Api\SearchCriteriaBuilder;
use Drupal\mage_ninja\DataModel\Product;
use Drupal\mage_ninja\Entity\MageNinjaProduct;
use Drupal\mage_ninja\Batch\Import;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
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
      /** @var \GuzzleHttp\Client $client */
      $client = Api::getClient();

      /** @var string $token */
      $token = Api::getAdminToken();

      /** @var array $authHeader */
      $authHeader = Api::getAuthHeader($token);

      $endpoint = 'V1/mage_ninja/product/' . $id;
      $options = [
        'headers' => $authHeader
      ];

      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->get($endpoint, $options);

      /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $serializer */
      $serializer = \Drupal::service('serializer');

      /** @var MageNinjaProduct $product */
      $product = $serializer->decode($response->getBody(), 'json');

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

      /** @var array $result */
      $result = $decoder->decode($response->getBody(), 'json');

      /** @var array $products */
      $products = $result['items'];

      return new JsonResponse($products);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }

  /**
   * Get the total product count from the endpoint.
   *
   * @return int|null
   */
  public function getProductCount() {
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
        ->add(['[pageSize]' => 1])
        ->add(['[currentPage]' => 1]);

      $endpoint = 'V1/products' . $searchCriteria;
      $options = [
        'headers' => $authHeader
      ];

      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->get($endpoint, $options);

      /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
      $decoder = \Drupal::service('serializer');

      /** @var array $result */
      $result = $decoder->decode($response->getBody(), 'json');

      /** @var int $count */
      $count = $result['total_count'];

      return $count;
    } catch (RequestException $e) {
      \Drupal::logger('mage_ninja')->error($e);

      return null;
    }
  }

  /**
   * Create a new MageNinjaProduct entity, if one does not exist with
   * the provided reference_id.
   *
   * @param int $id
   *  The reference id (Magento product ID)
   *
   * @return JsonExceptionResponse|JsonResponse
   */
  public function importById($id) {
    /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
    $decoder = \Drupal::service('serializer');

    /** @var int|array $productEntityId */
    $productEntityIds = \Drupal::entityQuery('mage_ninja_product')
      ->condition('reference_id', $id)
      ->execute();

    if(sizeOf($productEntityIds) === 1) {
      // Get actual ID from array where the ID is the key
      $productEntityId = reset($productEntityIds);

    } else if(sizeOf($productEntityIds) > 1){
      \Drupal::logger('mage_ninja')->error('Multiple MageNinjaProduct entities exist for reference_id: ' . $id . '.');

      return new JsonResponse([
        'success' => false
      ]);
    }
    
    try {
      /** @var JsonResponse $itemJson */
      $itemJson = $this->getById($id);
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }

    /** @var array $item */
    $item = $decoder->decode($itemJson->getContent(), 'json');

    /** @var Product $product */
    $product = new Product($item);

    if(empty($productEntityId)) {
      // This MageNinjaProduct entity does not yet exist, so create a new one.

      MageNinjaProduct::create($product->toArray())->save();

      \Drupal::logger('mage_ninja')->info('Import: product with ID: \'' . $id . '\' imported.');
    } else {
      // Update properties for this MageNinjaProduct entity.

      /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager */
      $entityTypeManager = \Drupal::service('entity_type.manager');

      try {
        /** @var \Drupal\mage_ninja\Entity\MageNinjaProductInterface $productEntity */
        $productEntity = $entityTypeManager
          ->getStorage('mage_ninja_product')
          ->load($productEntityId);
      } catch(InvalidPluginDefinitionException $e) {
        \Drupal::logger('mage_ninja')->error($e->getMessage());

        return new JsonExceptionResponse($e);
      }

      $productEntity->set('sku', $product->getSku());
      $productEntity->set('price', $product->getPricing()->getPrice());
      $productEntity->set('special_price', $product->getPricing()->getSpecialPrice());
      $productEntity->set('special_price_from', $product->getPricing()->getSpecialPriceFrom());
      $productEntity->set('special_price_to', $product->getPricing()->getSpecialPriceTo());

      try {
        $productEntity->save();
      } catch (EntityStorageException $e) {
        \Drupal::logger('mage_ninja')->error($e->getMessage());

        return new JsonExceptionResponse($e);
      }

      \Drupal::logger('mage_ninja')->info('Import: product with ID: \'' . $id . '\' updated.');
    }

    return new JsonResponse([
      'success' => true
    ]);
  }

  /**
   * Get all product Ids from Magento
   *
   * @return int[]|\Symfony\Component\HttpFoundation\JsonResponse
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

      /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
      $decoder = \Drupal::service('serializer');

      /** @var int[] $products */
      $productIds = $decoder->decode($response->getBody(), 'json');

      return $productIds;
    } catch (RequestException $e) {
      return new JsonExceptionResponse($e);
    }
  }
}