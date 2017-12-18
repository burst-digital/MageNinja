<?php

namespace Drupal\mage_ninja\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for mage_ninja product entities.
 */
class MageNinjaProductViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    /** @var array $data */
    $data = parent::getViewsData();

    return $data;
  }

}
