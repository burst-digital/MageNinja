<?php

namespace Drupal\mage_ninja;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the MageNinja_product entity type.
 *
 * @see \Drupal\mage_ninja\Entity\MageNinjaProduct
 */
class MageNinjaEntityAccessControlHandler extends EntityAccessControlHandler {
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