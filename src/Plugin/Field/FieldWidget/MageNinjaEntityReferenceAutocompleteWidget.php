<?php

namespace Drupal\mage_ninja\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;

/**
 * Implementation of the 'entity_reference_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "mage_ninja_entity_reference_autocomplete",
 *   label = @Translation("Autocomplete"),
 *   description = @Translation("An autocomplete text field."),
 *   field_types = {
 *     "mage_ninja_product"
 *   }
 * )
 */
class MageNinjaEntityReferenceAutocompleteWidget extends EntityReferenceAutocompleteWidget {

}
