<?php

namespace Drupal\hmc\Plugin\Field\FieldType;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

/**
 * Defines the 'hmc_entity_reference' entity field type.
 *
 * @FieldType(
 *   id = "hmc_product",
 *   label = @Translation("hmc product"),
 *   description = @Translation("A hmc product containing the reference ID to the product in Magento."),
 *   category = @Translation("Reference"),
 *   default_widget = "hmc_entity_reference_select",
 *   default_formatter = "hmc_entity_reference_entity_view",
 *   list_class = "\Drupal\webform\Plugin\Field\FieldType\HmcEntityReferenceFieldItemList",
 * )
 */
class HmcEntityReferenceItem extends EntityReferenceItem {
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
        'target_type' => 'hmc_product',
      ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'target_id' => [
          'description' => 'The ID of the hmc product entity.',
          'type' => 'varchar_ascii',
          'length' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
        ]
      ],
      'indexes' => [
        'target_id' => ['target_id'],
      ],
    ];
  }
}