<?php

namespace Drupal\hmc\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\EntityReferenceAutocompleteWidget;

/**
 * Implementation of the 'entity_reference_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "hmc_entity_reference_autocomplete",
 *   label = @Translation("Autocomplete"),
 *   description = @Translation("An autocomplete text field."),
 *   field_types = {
 *     "hmc"
 *   }
 * )
 */
class HmcEntityReferenceAutocompleteWidget extends EntityReferenceAutocompleteWidget {

}