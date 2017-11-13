<?php

namespace Drupal\mage_ninja\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining mage_ninja product entities.
 *
 * @ingroup mage_ninja
 */
interface MageNinjaProductInterface extends  ContentEntityInterface, EntityChangedInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the mage_ninja product reference ID.
   *
   * @return int
   *   Reference ID of the mage_ninja product.
   */
  public function getReferenceId();


  /**
   * Sets the mage_ninja product reference ID.
   *
   * @param int $id
   *   The Reference ID.
   *
   * @return \Drupal\mage_ninja\Entity\MageNinjaProductInterface
   *   The called mage_ninja product entity.
   */
  public function setReferenceId($id);

  /**
   * Gets the mage_ninja product name.
   *
   * @return string
   *   Name of the mage_ninja product.
   */
  public function getName();

  /**
   * Sets the mage_ninja product name.
   *
   * @param string $name
   *   The mage_ninja product name.
   *
   * @return \Drupal\mage_ninja\Entity\MageNinjaProductInterface
   *   The called mage_ninja product entity.
   */
  public function setName($name);

  /**
   * Gets the mage_ninja product creation timestamp.
   *
   * @return int
   *   Creation timestamp of the mage_ninja product.
   */
  public function getCreatedTime();

  /**
   * Sets the mage_ninja product creation timestamp.
   *
   * @param int $timestamp
   *   The mage_ninja product creation timestamp.
   *
   * @return \Drupal\mage_ninja\Entity\MageNinjaProductInterface
   *   The called mage_ninja product entity.
   */
  public function setCreatedTime($timestamp);

}
