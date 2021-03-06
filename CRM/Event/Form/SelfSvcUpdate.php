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
 * This class generates form components allowing an Event to be cancelled or transferred
 *
 */
class CRM_Event_Form_SelfSvcUpdate extends CRM_Core_Form {
  /**
   * particpant id
   *
   * @var string
   *
   */
  protected $_participant_id;
  /**
   * contact id
   *
   * @var string
   *
   */
  protected $_contact_id;
  /**
   * name of the particpant
   *
   * @var string
   *
   */
  protected $_contact_name;
  /**
   * email of participant
   *
   * @var string
   */
  protected $_contact_email;
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
   * @var string
   */
  protected $_part_values;
  /**
   * details of event registration values
   *
   * @var array
   */
  protected $_details = array();

  public function preProcess() {
    $config = CRM_Core_Config::singleton();
    $session = CRM_Core_Session::singleton();
    $this->_userContext = $session->readUserContext();
    $participant = $values = array();
    $this->_participant_id = CRM_Utils_Request::retrieve('pid', 'Positive', $this, FALSE, NULL, 'REQUEST');
    $params = array('id' => $this->_participant_id);
    $this->_participant = CRM_Event_BAO_Participant::getValues($params, $values, $participant);
    $this->_part_values = $values[$this->_participant_id];
    $this->set('values', $this->_part_values);
    //fetch Event by event_id, verify that this event can still be xferred/cancelled
    $this->_event_id = $this->_part_values['event_id'];
    $this->_contact_id = $this->_part_values['participant_contact_id'];
    $this->assign('action', $this->_action);
    if ($this->_participant_id) {
      $this->assign('participantId', $this->_id);
    }
    $event = array();
    $daoName = 'title';
    $this->_event_title = CRM_Event_BAO_Event::getFieldValue('CRM_Event_DAO_Event', $this->_event_id, $daoName);
    $daoName = 'start_date';
    $this->_event_start_date = CRM_Event_BAO_Event::getFieldValue('CRM_Event_DAO_Event', $this->_event_id, $daoName);
    list($displayName, $email) = CRM_Contact_BAO_Contact_Location::getEmailDetails($this->_contact_id);
    $this->_contact_name = $displayName;
    $this->_contact_email = $email;
    $details = array();
    $details = CRM_Event_BAO_Participant::participantDetails($this->_participant_id);
    $query = "
      SELECT cpst.name as status, cov.name as role, cp.fee_level, cp.fee_amount, cp.register_date, cp.status_id
      FROM civicrm_participant cp
      LEFT JOIN civicrm_participant_status_type cpst ON cpst.id = cp.status_id
      LEFT JOIN civicrm_option_value cov ON cov.value = cp.role_id and cov.option_group_id = 13
      WHERE cp.id = {$this->_participant_id}";
    $dao = CRM_Core_DAO::executeQuery($query, CRM_Core_DAO::$_nullArray);
    while ($dao->fetch()) {
      $details['status']  = $dao->status;
      $details['role'] = $dao->role;
      $details['fee_level']   = $dao->fee_level;
      $details['fee_amount'] = $dao->fee_amount;
      $details['register_date'] = $dao->register_date;
    }
    //verify participant status is still Registered
    if ($details['status'] != "Registered") {
      $status = "You are no longer registered for " . $this->_event_title;
      $session->setStatus($status, ts('Event status error.'), 'alert');
    }
    $query = "select start_date as start, selfcancelxfer_time as time from civicrm_event where id = " . $this->_event_id;
    $dao = CRM_Core_DAO::executeQuery($query, CRM_Core_DAO::$_nullArray);
    while ($dao->fetch()) {
      $time_limit  = $dao->time;
      $start_date = $dao->start;
    }
    if ($time_limit != NULL && $time_limit > 0) {
      $timenow = new Datetime("");
      $start_time = new Datetime($start_date);
      $interval = $timenow->diff($start_time);
      $days = $interval->format('%d');
      $hours   = $interval->format('%h');
      if ($hours <= $time_limit && $days < 1) {
        $status = ts("Less than ") . $time_limit . ts(" hours to start time, cannot transfer or cancel this event");
        $session->setStatus($status, ts('Oops.'), 'alert');
      }
    }
    $this->assign('details', $details);
    $this->selfsvcupdateUrl = CRM_Utils_System::url('civicrm/event/selfsvcupdate', "reset=1&id={$this->_participant_id}&id=0");
    $this->selfsvcupdateText = ts('Update');
    $this->selfsvcupdateButtonText = ts('Update');
    // Based on those ids retrieve event and verify it is eligible
    // for self update (event.start_date > today, event can be 'self_updated'
    // retrieve contact name and email, and let user verify his/her identity
  }

  public function buildQuickForm() {
    $this->add('text', 'email', ts('Email'), ts($this->_contact_email), TRUE);
    $this->add('text', 'participant', ts('Participant name'), ts($this->_contact_name), TRUE);
    $this->add('select', 'action', 'Action', array('-select-', 'Transfer', 'Cancel'));
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
      ),
      array(
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ),
    ));
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    $this->_defaults = array();
    $this->_defaults['email'] = $this->_contact_email;
    $this->_defaults['participant'] = $this->_contact_name;
    $this->_defaults['details'] = $this->_details;
    return $this->_defaults;
  }

  public function postProcess() {
    //if selection is cancel, cancel this participant' registration, process refund
    //if transfer, process form to allow selection of transferree
    $params = $this->controller->exportValues($this->_name);
    $action = $params['action'];
    if ($action == "1") {
      $action = "Transfer Event";
      $this->transferParticipant($params);
    }
    elseif ($action == "2") {
      $action = "Cancel Event";
      $this->cancelParticipant($params);
    }
  }

  public function transferParticipant($params) {
    $transferUrl = 'civicrm/event/form/selfsvctransfer';
    $url = CRM_Utils_System::url('civicrm/event/selfsvctransfer', 'reset=1&action=add&pid=' . $this->_participant_id);
    $this->controller->setDestination($url);
    $session = CRM_Core_Session::singleton();
    $session->replaceUserContext($url);
  }

  public function cancelParticipant($params) {
    //set participant record status to Cancelled, refund payment if possible
    // send email to participant and admin, and log Activity
    $value = array();
    $value['id'] = $this->_participant_id;
    $cancelledId = array_search('Cancelled',
    CRM_Event_PseudoConstant::participantStatus(NULL, "class = 'Negative'"));
    $value['status_id'] = $cancelledId;
    CRM_Event_BAO_Participant::create($value);
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
    $query = "SELECT * FROM civicrm_participant WHERE id = {$this->_participant_id}";
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
    $contactIds[$this->_contact_id] = $this->_contact_id;
    list($currentContactDetails) = CRM_Utils_Token::getTokenDetails($contactIds, NULL,
      FALSE, FALSE, NULL, array(),
      'CRM_Event_BAO_Participant'
    );
    foreach ($currentContactDetails as $contactId => $contactValues) {
      $contactDetails[$this->_contact_id] = $contactValues;
    }
    //send a 'cancelled' email to user, and cc the event's cc_confirm email
    $mail = CRM_Event_BAO_Participant::sendTransitionParticipantMail($this->_participant_id,
      $participantDetails[$this->_participant_id],
      $eventDetails[$this->_event_id],
      $contactDetails[$this->_contact_id],
      $domainValues,
      "Cancelled",
      ""
    );
    $statusMsg = ts('Event registration information for %1 has been updated.', array(1 => $this->_contact_name));
    $statusMsg .= ' ' . ts('A cancellation email has been sent to %1.', array(1 => $this->_contact_email));
    CRM_Core_Session::setStatus($statusMsg, ts('Saved'), 'success');
  }

}
