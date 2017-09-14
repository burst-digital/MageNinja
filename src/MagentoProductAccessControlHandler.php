<?php

namespace Drupal\hmc;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Magento product entity.
 *
 * @see \Drupal\hmc\Entity\MagentoProduct.
 */
class MagentoProductAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\hmc\Entity\MagentoProductInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view magento product entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit magento product entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete magento product entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add magento product entities');
  }

}
