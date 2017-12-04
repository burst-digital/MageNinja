<?php

namespace Drupal\mage_ninja\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mage_ninja\Controller\ProductController;

class ConfigForm extends ConfigFormBase {
  /**
   * @var int PAGE_SIZE
   *  The amount of items that will be requested per page.
   */
  const PAGE_SIZE = 100;

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
    return 'mage_ninja_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mage_ninja.settings');

    $form['import'] = [
      '#type' => 'details',
      '#title' => t('Import'),
      '#open' => TRUE,
    ];

    $form['import']['import_all'] = [
      '#type' => 'submit',
      '#value' => t('Import all products'),
      '#submit' => ['::importAll'],
    ];

    $form['connection'] = [
      '#type' => 'details',
      '#title' => t('Connection'),
      '#open' => TRUE,
    ];

    $form['connection']['base_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento base URI'),
      '#default_value' => $config->get('base_uri'),
      '#required' => TRUE
    ];

    $form['connection']['admin_username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Magento admin username'),
      '#default_value' => $config->get('admin_username'),
      '#required' => TRUE
    ];

    $form['connection']['admin_password'] = [
      '#type' => 'password',
      '#title' => $this->t('Magento admin password'),
      '#default_value' => $config->get('admin_password'),
      '#required' => TRUE
    ];

    return parent::buildForm($form, $form_state);;
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

  /**
   * Import all Magento products into Drupal.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function importAll(array &$form, FormStateInterface $form_state) {
    /** @var ProductController $controller */
    $controller = new ProductController();

    /** @var int $productCounnt */
    $productCount = $controller->getProductCount();

    /** @var int $currentPage */
    $currentPage = 1;

    /** @var array $operations */
    $operations = [];

    /** @var int $totalPages */
    // Round up to make sure the final page is also processed if it
    // contains less than PAGE_SIZE items.
    $totalPages = ceil($productCount / self::PAGE_SIZE);

    do {
      $operations[] = ['\Drupal\mage_ninja\Batch\Import::process', [$currentPage, self::PAGE_SIZE]];
      $currentPage++;
    } while($totalPages >= $currentPage);


    $batch = [
      'title' => t('MageNinja processing batch import'),
      'operations' => $operations,
      'init_message' => t('MageNinja initializing batch import'),
      'progress_message' => t('MageNinja processed @current out of @total (@remaining remaining). Estimated time remaining: @estimate (@elapsed elapsed).'),
      'error_message' => t('MageNinja encountered an error during the batch import process.')
    ];

    batch_set($batch);
  }
}