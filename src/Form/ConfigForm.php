<?php

namespace Drupal\hmc\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['hmc.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hmc_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('hmc.settings');

    $form['admin_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Admin username'),
      '#default_value' => $config->get('hmc.admin_username'),
      '#required' => TRUE
    ];

    $form['admin_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Admin password'),
      '#default_value' => $config->get('hmc.admin_password'),
      '#required' => TRUE
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('hmc.settings');

    $config->set('admin_username', $form_state->getValue('admin_username'));
    $config->set('admin_password', $form_state->getValue('admin_password'));

    $config->save();

    parent::submitForm($form, $form_state);
  }


}