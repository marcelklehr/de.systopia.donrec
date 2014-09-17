<?php
/*-------------------------------------------------------+
| SYSTOPIA Donation Receipts Extension                   |
| Copyright (C) 2013-2014 SYSTOPIA                       |
| Author: B. Endres (endres -at- systopia.de)    |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| TODO: License                                          |
+--------------------------------------------------------*/


require_once 'donrec.civix.php';
require_once 'CRM/Donrec/DataStructure.php';

/**
 * Implementation of hook_civicrm_config
 */
function donrec_civicrm_config(&$config) {
  _donrec_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function donrec_civicrm_xmlMenu(&$files) {
  _donrec_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function donrec_civicrm_install() {
  // create database tables
  $config = CRM_Core_Config::singleton();
  $sql = file_get_contents(dirname( __FILE__ ) .'/sql/donrec.sql', true);
  CRM_Utils_File::sourceSQLFile($config->dsn, $sql, NULL, true);

  return _donrec_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function donrec_civicrm_uninstall() {
  return _donrec_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function donrec_civicrm_enable() {
  // create/update custom groups
  CRM_Donrec_DataStructure::update();
  // install default template
  CRM_Donrec_Logic_Templates::setDefaultTemplate();
  
  return _donrec_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function donrec_civicrm_disable() {
  return _donrec_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function donrec_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _donrec_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function donrec_civicrm_managed(&$entities) {
  return _donrec_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 */
function donrec_civicrm_caseTypes(&$caseTypes) {
  _donrec_civix_civicrm_caseTypes($caseTypes);
}

/**
* Add an action for creating donation receipts after doing a search
*
* @param string $objectType specifies the component
* @param array $tasks the list of actions
*
* @access public
*/
function donrec_civicrm_searchTasks($objectType, &$tasks) {
  if ($objectType == 'contact') {
    $tasks[] = array(
    'title' => ts('Issue donation receipt(s)'),
    'class' => 'CRM_Donrec_Form_Task_DonrecTask',
    'result' => false);
  }
}

/**
 * Set permissions for runner/engine API call
 */
function donrec_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  // TODO: adjust to correct permission
  $permissions['donation_receipt_engine']['next'] = array('access CiviCRM');
}

/**
 * Set settings
 */
function donrec_civicrm_alterSettingsFolders(&$metaDataFolders = NULL){
  static $configured = FALSE;
  if ($configured) return;
  $configured = TRUE;

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'settings';
  if(!in_array($extDir, $metaDataFolders)){
    $metaDataFolders[] = $extDir;
  }
}
