<?php

/**
 * @file
 * Contains magento_product.page.inc.
 *
 * Page callback for Magento product entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Magento product templates.
 *
 * Default template: magento_product.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_magento_product(array &$variables) {
  // Fetch MagentoProduct Entity Object.
  $magento_product = $variables['elements']['#magento_product'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
