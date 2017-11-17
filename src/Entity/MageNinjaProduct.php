<?php

namespace Drupal\mage_ninja\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the mage_ninja product entity.
 *
 * @ingroup mage_ninja
 *
 * @ContentEntityType(
 *   id = "mage_ninja_product",
 *   label = @Translation("mage_ninja product"),
 *   handlers = {
 *     "access" = "Drupal\mage_ninja\MageNinjaEntityAccessControlHandler"
 *   },
 *   base_table = "mage_ninja_product",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "name",
 *   }
 * )
 */
class MageNinjaProduct extends ContentEntityBase implements MageNinjaProductInterface {
  use EntityChangedTrait;

  // Set price decimal scale and precision to the same values as in the Magento database.
  /**
   * @var int PRICE_PRECISION
   *  The total amount of digits in the number.
   */
  const PRICE_PRECISION = 12;

  /**
   * @var int PRICE_PRECISION
   *  The amount of digits to the right of the decimal point in the number.
   */
  const PRICE_SCALE = 4;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $fields */
    $fields = parent::baseFieldDefinitions($entity_type);

    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the mage_ninja product entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the mage_ninja product entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the mage_ninja product entity.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the mage_ninja product is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));


    /*
     * Magento product fields
     */

    // Magento product ID
    $fields['reference_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Reference ID'))
      ->setDescription(t('The product ID in Magento.'))
      ->setReadOnly(TRUE);

    $fields['sku'] = BaseFieldDefinition::create('string')
      ->setLabel(t('SKU'))
      ->setDescription(t('The SKU in Magento.'))
      ->setReadOnly(TRUE);

    $fields['price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Price'))
      ->setDescription(t('The price in Magento.'))
      ->setReadOnly(TRUE)
      ->setSetting('scale', self::PRICE_SCALE)
      ->setSetting('precision', self::PRICE_PRECISION);

    $fields['special_price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Special price'))
      ->setDescription(t('The special price in Magento.'))
      ->setReadOnly(TRUE)
      ->setSetting('scale', self::PRICE_SCALE)
      ->setSetting('precision', self::PRICE_PRECISION);

    $fields['special_price_from'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Special price from'))
      ->setDescription(t('The datetime from which the special price is active.'))
      ->setReadOnly(TRUE);

    $fields['special_price_to'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Special price to'))
      ->setDescription(t('The datetime to which the special price is active.'))
      ->setReadOnly(TRUE);

    $fields['stock_status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('In stock'))
      ->setDescription(t('The stock status in Magento'))
      ->setReadOnly(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getReferenceId() {
    return $this->get('reference_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setReferenceId($id) {
    $this->set('reference_id', $id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }
}
