<?php

namespace Drupal\hmc;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the hmc_product entity type.
 *
 * @see \Drupal\hmc\Entity\HmcProduct
 */
class HmcEntityAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    // TODO: configure permissions
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // TODO: configure permissions
    return AccessResult::allowed();
  }
}