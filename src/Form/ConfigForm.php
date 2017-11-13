<?php

namespace Drupal\mage_ninja\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mage_ninja.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'MageNinja_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('mage_ninja.settings');

    $form['base_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento base URI'),
      '#default_value' => $config->get('base_uri'),
      '#required' => TRUE
    ];

    $form['admin_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento admin username'),
      '#default_value' => $config->get('admin_username'),
      '#required' => TRUE
    ];

    $form['admin_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Magento admin password'),
      '#default_value' => $config->get('admin_password'),
      '#required' => TRUE
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('mage_ninja.settings');

    $config->set('base_uri', $form_state->getValue('base_uri'));
    $config->set('admin_username', $form_state->getValue('admin_username'));
    $config->set('admin_password', $form_state->getValue('admin_password'));

    $config->save();

    parent::submitForm($form, $form_state);
  }


}