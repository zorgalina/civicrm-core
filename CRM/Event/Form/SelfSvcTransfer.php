<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.7                                          |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 * This class generates form components transferring an Event to another participant
 *
 */
class CRM_Event_Form_SelfSvcTransfer extends CRM_Core_Form {
  /**
   * from particpant id
   *
   * @var string
   *
   */
  protected $_from_participant_id;
  /**
   * from contact id
   *
   * @var string
   *
   */
  protected $_from_contact_id;
  /**
   * last name of the particpant to transfer to
   *
   * @var string
   *
   */
  protected $_to_contact_last_name;
  /**
   * first name of the particpant to transfer to
   *
   * @var string
   *
   */
  protected $_to_contact_first_name;
  /**
   * email of participant
   *
   *
   * @var string
   */
  protected $_to_contact_email;
  /**
   * _to_contact_id
   *
   * @var string
   */
  protected $_to_contact_id;
  /**
   * event to be cancelled/transferred
   *
   * @var string
   */
  protected $_event_id;
  /**
   * event title
   *
   * @var string
   */
  protected $_event_title;
  /**
   * event title
   *
   * @var string
   */
  protected $_event_start_date;
  /**
   * action
   *
   * @var string
   */
  protected $_action;
  /**
   * participant object
   *
   * @var string
   */
  protected $_participant = array();
  /**
   * particpant values
   *
   * @array string
   */
  protected $_part_values;
  /**
   * details
   *
   * @array string
   */
  protected $_details = array();
  /**
   * line items
   *
   * @array string
   */
  protected $_line_items = array();
  /**
   * contact_id
   *
   * @array string
   */
  protected $contact_id;

  public function preProcess() {
    $config = CRM_Core_Config::singleton();
    $session = CRM_Core_Session::singleton();
    $this->_userContext = $session->readUserContext();
    $this->_from_participant_id = CRM_Utils_Request::retrieve('pid', 'Positive', $this, FALSE, NULL, 'REQUEST');
    $params = array('id' => $this->_from_participant_id);
    $participant = $values = array();
    $this->_participant = CRM_Event_BAO_Participant::getValues($params, $values, $participant);
    $this->_part_values = $values[$this->_from_participant_id];
    $this->set('values', $this->_part_values);
    $this->_event_id = $this->_part_values['event_id'];
    $this->_from_contact_id = $this->_part_values['participant_contact_id'];
    $this->assign('action', $this->_action);
    if ($this->_from_participant_id) {
      $this->assign('participantId', $this->_id);
    }
    $event = array();
    $daoName = 'title';
    $this->_event_title = CRM_Event_BAO_Event::getFieldValue('CRM_Event_DAO_Event', $this->_event_id, $daoName);
    $daoName = 'start_date';
    $this->_event_start_date = CRM_Event_BAO_Event::getFieldValue('CRM_Event_DAO_Event', $this->_event_id, $daoName);
    list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($this->_from_contact_id);
    $this->_contact_name = $displayName;
    $this->_contact_email = $email;
    $details = array();
    $details = CRM_Event_BAO_Participant::participantDetails($this->_from_participant_id);
    $query = "
      SELECT cpst.name as status, cov.name as role, cp.fee_level, cp.fee_amount, cp.register_date
      FROM civicrm_participant cp
      LEFT JOIN civicrm_participant_status_type cpst ON cpst.id = cp.status_id
      LEFT JOIN civicrm_option_value cov ON cov.value = cp.role_id and cov.option_group_id = 13
      WHERE cp.id = {$this->_from_participant_id}";
    $dao = CRM_Core_DAO::executeQuery($query, CRM_Core_DAO::$_nullArray);
    while ($dao->fetch()) {
      $details['status']  = $dao->status;
      $details['role'] = $dao->role;
      $details['fee_level']   = $dao->fee_level;
      $details['fee_amount'] = $dao->fee_amount;
      $details['register_date'] = $dao->register_date;
    }
    $this->assign('details', $details);
    //This participant row will be cancelled.  Get line item(s) to cancel
    $this->selfsvctransferUrl = CRM_Utils_System::url('civicrm/event/selfsvcupdate',
      "reset=1&id={$this->_from_participant_id}&id=0");
    $this->selfsvctransferText = ts('Update');
    $this->selfsvctransferButtonText = ts('Update');
  }

  public function buildQuickForm() {
    $this->add('text', 'email', ts('To Email'), ts($this->_contact_email), TRUE);
    $this->add('text', 'last_name', ts('To Last name'), ts($this->_to_contact_last_name), TRUE);
    $this->add('text', 'first_name', ts('To First name'), ts($this->_to_contact_first_name), TRUE);
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),),
      )
    );
    $this->addFormRule(array('CRM_Event_Form_SelfSvcTransfer', 'formRule'), $this);
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $this->_defaults = array();
    return $this->_defaults;
  }

  public static function formRule($fields, $files, $self) {
    $errors = array();
    //check that either an email or firstname+lastname is included in the form(CRM-9587)
    $to_contact_id = self::checkProfileComplete($fields, $errors, $self);
    //To check if the user is already registered for the event(CRM-2426)
    self::checkRegistration($fields, $self, $to_contact_id);
    //return parent::formrule($fields, $files, $self);
    return empty($errors) ? TRUE : $errors;
  }

  public static function checkProfileComplete($fields, &$errors, $self) {
    $email = '';
    foreach ($fields as $fieldname => $fieldvalue) {
      if (substr($fieldname, 0, 5) == 'email' && $fieldvalue) {
        $email = $fieldvalue;
      }
    }
    if (!$email && !(CRM_Utils_Array::value('first_name', $fields) &&
      CRM_Utils_Array::value('last_name', $fields))) {
      $defaults = $params = array('id' => $eventId);
      CRM_Event_BAO_Event::retrieve($params, $defaults);
      $message = ts("Mandatory fields (first name and last name, OR email address) are missing from this form.");
      $errors['_qf_default'] = $message;
    }
    $contact = CRM_Contact_BAO_Contact::matchContactOnEmail($email, "");
    $contact_id = $contact->contact_id;
    if ($contact_id == NULL) {
      $params = array(
        'email-Primary' => CRM_Utils_Array::value('email', $fields, NULL),
        'first_name' => CRM_Utils_Array::value('first_name', $fields, NULL),
        'last_name' => CRM_Utils_Array::value('last_name', $fields, NULL),
        'is_deleted' => CRM_Utils_Array::value('is_deleted', $fields, FALSE,);
      //create new contact for this name/email pair
      //if new contact, no need to check for contact already registered
      $contact_id = CRM_Contact_BAO_Contact::createProfileContact($params, $fields, $contact_id);
    }
    return $contact_id;
  }

  public static function checkRegistration($fields, $self, $contact_id) {
    // verify whether this contact already registered for this event
    $session = CRM_Core_Session::singleton();
    $contact_details = CRM_Contact_BAO_Contact::getContactDetails($contact_id);
    $display_name = $contact_details[0];
    $query = "select event_id from civicrm_participant where contact_id = " . $contact_id;
    $dao = CRM_Core_DAO::executeQuery($query, CRM_Core_DAO::$_nullArray);
    while ($dao->fetch()) {
      $to_event_id[]  = $dao->event_id;
    }
    if ($to_event_id != NULL) {
      foreach ($to_event_id as $id) {
        if ($id == $self->_event_id) {
          $status = $display_name . ts(" is already registered for this event");
          $session->setStatus($status, ts('Oops.'), 'alert');
        }
      }
    }
  }

  public function postProcess() {
    //For transfer, process form to allow selection of transferree
    $params = $this->controller->exportValues($this->_name);
    //cancel 'from' participant row
    $query = "select contact_id from civicrm_email where email = '" . $params['email'] . "'";
    $dao = CRM_Core_DAO::executeQuery($query, CRM_Core_DAO::$_nullArray);
    while ($dao->fetch()) {
      $contact_id  = $dao->contact_id;
    }
    $from_participant = $params = array();
    $query = "select role_id, source, fee_level, is_test, is_pay_later, fee_amount, discount_id, fee_currency,campaign_id, discount_amount from civicrm_participant where id = " . $this->_from_participant_id;
    $dao = CRM_Core_DAO::executeQuery($query, CRM_Core_DAO::$_nullArray);
    $value_to = array();
    while ($dao->fetch()) {
      $value_to['role_id'] = $dao->role_id;
      $value_to['source'] = $dao->source;
      $value_to['fee_level'] = $dao->fee_level;
      $value_to['is_test'] = $dao->is_test;
      $value_to['is_pay_later'] = $dao->is_pay_later;
      $value_to['fee_amount'] = $dao->fee_amount;
    }
    $value_to['contact_id'] = $contact_id;
    $value_to['event_id'] = $this->_event_id;
    $value_to['status_id'] = 1;
    $value_to['register_date'] = date("Y-m-d");
    //first create the new participant row -don't set registered_by yet or email won't be sent
    $participant = CRM_Event_BAO_Participant::create($value_to);
    //send a confirmation email to the new participant
    $err = $this->participantTransfer($participant);
    if (!$err) {
      $statusMsg = "Failed to send confirmation email";
      CRM_Core_Session::setStatus($statusMsg, ts('Error'), 'error');
    }
    //now update registered_by_id
    $query = "UPDATE civicrm_participant cp SET cp.registered_by_id = %1 WHERE  cp.id = ({$participant->id})";
    $params = array(1 => array($this->_from_participant_id, 'Integer'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    //copy line items to new participant
    $line_items = CRM_Price_BAO_LineItem::getLineItems($this->_from_participant_id);
    foreach ($line_items as $item) {
      $item['entity_id'] = $participant->id;
      $item['id'] = NULL;
      $item['entity_table'] = "civicrm_participant";
      $new_item = CRM_Price_BAO_LineItem::create($item);
    }
    //now cancel the from participant record, leaving the original line-item(s)
    $value_from = array();
    $value_from['id'] = $this->_from_participant_id;
    $cancelledId = array_search('Cancelled',
      CRM_Event_PseudoConstant::participantStatus(NULL, "class = 'Negative'"));
    $value_from['status_id'] = $cancelledId;
    CRM_Event_BAO_Participant::create($value_from);
    $this->sendCancellation();
    list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($contact_id);
    $statusMsg = ts('Event registration information for %1 has been updated.', array(1 => $displayName));
    $statusMsg .= ' ' . ts('A confirmation email has been sent to %1.', array(1 => $email));
    CRM_Core_Session::setStatus($statusMsg, ts('Saved'), 'success');
  }

  public function participantTransfer($participant) {
    $contactDetails = array();
    $contactIds[] = $participant->contact_id;
    list($currentContactDetails) = CRM_Utils_Token::getTokenDetails($contactIds, NULL,
      FALSE, FALSE, NULL, array(), 'CRM_Event_BAO_Participant');
    foreach ($currentContactDetails as $contactId => $contactValues) {
      $contactDetails[$contactId] = $contactValues;
    }
    $participantRoles = CRM_Event_PseudoConstant::participantRole();
    $participantDetails = array();
    $query = "SELECT * FROM civicrm_participant WHERE id = " . $participant->id;
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      $participantDetails[$dao->id] = array(
        'id' => $dao->id,
        'role' => $participantRoles[$dao->role_id],
        'is_test' => $dao->is_test,
        'event_id' => $dao->event_id,
        'status_id' => $dao->status_id,
        'fee_amount' => $dao->fee_amount,
        'contact_id' => $dao->contact_id,
        'register_date' => $dao->register_date,
        'registered_by_id' => $dao->registered_by_id,
      );
    }
    $domainValues = array();
    if (empty($domainValues)) {
      $domain = CRM_Core_BAO_Domain::getDomain();
      $tokens = array(
        'domain' =>
        array(
          'name',
          'phone',
          'address',
          'email',
        ),
        'contact' => CRM_Core_SelectValues::contactTokens(),
      );
      foreach ($tokens['domain'] as $token) {
        $domainValues[$token] = CRM_Utils_Token::getDomainTokenReplacement($token, $domain);
      }
    }
    $eventDetails = array();
    $eventParams = array('id' => $participant->event_id);
    CRM_Event_BAO_Event::retrieve($eventParams, $eventDetails[$participant->event_id]);
    //get default participant role.
    $eventDetails[$participant->event_id]['participant_role'] = CRM_Utils_Array::value($eventDetails[$participant->event_id]['default_role_id'], $participantRoles);
    //get the location info
    $locParams = array(
      'entity_id' => $participant->event_id,
      'entity_table' => 'civicrm_event',
    );
    $eventDetails[$participant->event_id]['location'] = CRM_Core_BAO_Location::getValues($locParams, TRUE);
    //need to set a flag for 'transfer' in sendTransactionParticipantEmail or Confirm email won't be sent
    $res = CRM_Event_BAO_Participant::sendTransitionParticipantMail($participant->id, $participantDetails[$participant->id], $eventDetails[$participant->event_id], $contactDetails[$participant->contact_id], $domainValues, "Confirm", TRUE);
    //now registered_id can be updated (mail won't be send if it is set
    return res;
  }

  public function sendCancellation() {
    $domainValues = array();
    $domain = CRM_Core_BAO_Domain::getDomain();
    $tokens = array(
      'domain' =>
      array(
        'name',
        'phone',
        'address',
        'email',
      ),
      'contact' => CRM_Core_SelectValues::contactTokens(),
    );
    foreach ($tokens['domain'] as $token) {
      $domainValues[$token] = CRM_Utils_Token::getDomainTokenReplacement($token, $domain);
    }
    $participantRoles = array();
    $participantRoles = CRM_Event_PseudoConstant::participantRole();
    $participantDetails = array();
    $query = "SELECT * FROM civicrm_participant WHERE id = {$this->_from_participant_id}";
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      $participantDetails[$dao->id] = array(
        'id' => $dao->id,
        'role' => $participantRoles[$dao->role_id],
        'is_test' => $dao->is_test,
        'event_id' => $dao->event_id,
        'status_id' => $dao->status_id,
        'fee_amount' => $dao->fee_amount,
        'contact_id' => $dao->contact_id,
        'register_date' => $dao->register_date,
        'registered_by_id' => $dao->registered_by_id,
      );
    }
    $eventDetails = array();
    $eventParams = array('id' => $this->_event_id);
    CRM_Event_BAO_Event::retrieve($eventParams, $eventDetails[$this->_event_id]);
    //get default participant role.
    $eventDetails[$this->_event_id]['participant_role'] = CRM_Utils_Array::value($eventDetails[$this->_event_id]['default_role_id'], $participantRoles);
    //get the location info
    $locParams = array('entity_id' => $this->_event_id, 'entity_table' => 'civicrm_event');
    $eventDetails[$this->_event_id]['location'] = CRM_Core_BAO_Location::getValues($locParams, TRUE);
    //get contact details
    $contactIds[$this->_contact_id] = $this->_from_contact_id;
    list($currentContactDetails) = CRM_Utils_Token::getTokenDetails($contactIds, NULL,
      FALSE, FALSE, NULL, array(),
      'CRM_Event_BAO_Participant'
    );
    foreach ($currentContactDetails as $contactId => $contactValues) {
      $contactDetails[$this->_from_contact_id] = $contactValues;
    }
    //send a 'cancelled' email to user, and cc the event's cc_confirm email
    $mail = CRM_Event_BAO_Participant::sendTransitionParticipantMail($this->_participant_id,
      $participantDetails[$this->_from_participant_id],
      $eventDetails[$this->_event_id],
      $contactDetails[$this->_from_contact_id],
      $domainValues,
      "Cancelled",
      ""
    );
    $statusMsg = ts('Event registration information for %1 has been updated.', array(1 => $this->_contact_name));
    $statusMsg .= ' ' . ts('A cancellation email has been sent to %1.', array(1 => $this->_contact_email));
    CRM_Core_Session::setStatus($statusMsg, ts('Saved'), 'success');
  }

}
