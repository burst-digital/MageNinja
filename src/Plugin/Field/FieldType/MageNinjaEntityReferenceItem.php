<?php

namespace Drupal\mage_ninja\Plugin\Field\FieldType;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'MageNinja_entity_reference' entity field type.
 *
 * @FieldType(
 *   id = "mage_ninja_product",
 *   label = @Translation("MageNinja Product"),
 *   description = @Translation("A mage_ninja product containing the reference ID to the product in Magento."),
 *   category = @Translation("Reference"),
 *   default_widget = "entity_reference_autocomplete",
 *   list_class = "\Drupal\mage_ninja\Plugin\Field\FieldType\MageNinjaEntityReferenceFieldItemList",
 * )
 */
class MageNinjaEntityReferenceItem extends EntityReferenceItem {
  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
        'target_type' => 'MageNinja_product',
      ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'target_id' => [
          'description' => 'The mage_ninja product entity.',
          'type' => 'varchar_ascii',
          'length' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
        ]
      ],
      'indexes' => [
        'target_id' => ['target_id'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function getPreconfiguredOptions() {
    return [];
  }
}