<?php

namespace Drupal\hmc\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Magento product entities.
 *
 * @ingroup hmc
 */
interface MagentoProductInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Magento product name.
   *
   * @return string
   *   Name of the Magento product.
   */
  public function getName();

  /**
   * Sets the Magento product name.
   *
   * @param string $name
   *   The Magento product name.
   *
   * @return \Drupal\hmc\Entity\MagentoProductInterface
   *   The called Magento product entity.
   */
  public function setName($name);

  /**
   * Gets the Magento product creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Magento product.
   */
  public function getCreatedTime();

  /**
   * Sets the Magento product creation timestamp.
   *
   * @param int $timestamp
   *   The Magento product creation timestamp.
   *
   * @return \Drupal\hmc\Entity\MagentoProductInterface
   *   The called Magento product entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Magento product published status indicator.
   *
   * Unpublished Magento product are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Magento product is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Magento product.
   *
   * @param bool $published
   *   TRUE to set this Magento product to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\hmc\Entity\MagentoProductInterface
   *   The called Magento product entity.
   */
  public function setPublished($published);

}
