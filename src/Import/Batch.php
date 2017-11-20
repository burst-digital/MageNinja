<?php

namespace Drupal\mage_ninja\Import;

use Drupal\mage_ninja\Entity\MageNinjaProduct;
use Drupal\mage_ninja\Controller\ProductController;

class Batch {
  /**
   * Processes a page of items.
   *
   * @param $currentPage
   *  The page number
   *
   * @param $pageSize
   *  The amount of items on the page
   */
  public static function process($currentPage, $pageSize) {
    $controller = new ProductController();

    /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
    $response =  $controller->getByPage($currentPage, $pageSize);

    /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface $decoder */
    $decoder = \Drupal::service('serializer');

    /** @var array $items */
    $items = $decoder->decode($response->getContent(), 'json');

    foreach ($items as $item) {
      /** @var MageNinjaProduct $productEntity */
      $productEntity = \Drupal::entityQuery('mage_ninja_product')
        ->condition('reference_id', $item['id'])
        ->execute();

      if (empty($productEntity)) {
        $product = [
          'reference_id' => $item['id'],
          'sku' => $item['sku'],
          'name' => $item['name'],
          'price' => $item['price'],
        ];
        
        if(isset($item['custom_attributes'])) {
          foreach($item['custom_attributes'] as $attribute) {
            if($attribute['attribute_code'] === 'special_price') {
              $product['special_price'] = $attribute['value'];
            }

            if($attribute['attribute_code'] === 'special_from_date') {
              $product['special_price_from'] = $attribute['value'];
            }

            if($attribute['attribute_code'] === 'special_to_date') {
              $product['special_price_to'] = $attribute['value'];
            }
          }
        }

        MageNinjaProduct::create($product)->save();
      }
    }
  }
}