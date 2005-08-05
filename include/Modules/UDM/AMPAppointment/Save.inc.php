<?php

define( 'AMP_ERROR_SCHEDULE_APPOINTMENT_EMAIL_NOT_SENT', 'Appointment Notification Email not sent to %s');

require_once ( 'AMP/UserData/Plugin/Save.inc.php' );
require_once ( 'Modules/Schedule/Appointment.inc.php' );
require_once ( 'Modules/Schedule/Schedule.php' );
require_once ( 'Modules/Schedule/Lookups.inc.php' );
require_once ( 'Modules/Schedule/Appointment/Form.inc.php' );
require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/System/Email.inc.php' );

class UserDataPlugin_Save_AMPAppointment extends UserDataPlugin_Save {

	var $short_name = "Appointment";

	var $options = array(
		'schedule_id' => array( 
			'available' => true,
			'type' 	=> 'select',
			'default' => 1,
			'values'  => 'Lookup(schedules, id, name)' ),
        'email_contact_schedule' => array(
            'type' => 'select',
            'available' => true,
            'default' => false,
            'label' => 'Confirmation Email Template for Schedule Contact' ),
        'email_contact_appointment' => array(
            'type' => 'select',
            'available' => true,
            'default' => false,
            'label' => 'Confirmation Email Template for Appointment Contact' )

		);


	function UserDataPlugin_Save_AMPAppointment ( &$udm, $plugin_instance=null ) {
		$this->init( $udm, $plugin_instance );
	}

	function _register_fields_dynamic() {
		$options = $this->getOptions();
		$schedule = &new Schedule( $this->dbcon, $options['schedule_id'] );

        $open_appts =  $schedule->describeOpenItems();
        $header_prefix  =  'AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_';
        $header_descriptor = 'AVAILABLE';
        $choose_default = null;
        if (isset( $_GET['action_id'] ) && ( $item_id = $_GET['action_id'] )) {
            if (!isset( $open_appts[ $item_id ] )) {
                $header_prefix .= 'REQUESTED_';
                $open_appts = array();
            } else {
                $single_appt = array();
                $single_appt[ $item_id ] = $open_appts[ $item_id ];
                $open_appts = $single_appt;
                $choose_default = $item_id;
            }
        }

        if (empty( $open_appts )|| !$open_appts)    $header_descriptor = 'UNAVAILABLE';

        $this->fields = array( 
            'Appointments' => array(
                'type' => 'header', 
                'label' => sprintf( constant( $header_prefix . $header_descriptor ), $schedule->getName() ),
                'enabled' => true, 
                'public' => true )
            );

		
        if (empty( $open_appts )|| !$open_appts) return;

		$this->fields[ 'action_id'] = array(
				'type' => 'radiogroup',
				'public' => true,
				'enabled' => true,
				'default' => $choose_default,
                'label' => 'Available Times',
				'required' => true,
				'values'  => $open_appts
			);

	}

	function getSaveFields() {
		return array( 'action_id' );
	}

	function save( $data ) {
		$options = $this->getOptions();
        if (!( isset($this->udm->uid))) {
            $this->udm->errorMessage( "Invalid Contact Info" );
            return false; 
        }

        if (!( isset($data['action_id']) && $data['action_id'])) {
            $this->udm->errorMessage( "No Appointment Selected" );
            return ;
        }

		$schedule = &new Schedule( $this->dbcon, $options['schedule_id'] );

		if (!$schedule->makeAppointment( $this->udm->uid, $data['action_id'] )) {
            $this->udm->errorMessage( "The requested schedule time is not avaiable" );
            return false;
        }

        $item = &$schedule->getScheduleItem( $data['action_id'] );
        $appt_contact_data = $this->udm->getData();

        $recipients = array();
        if (isset($options['email_contact_schedule']) && $options['email_contact_schedule']) {
            $recipients['schedule'] = $item->getOwnerEmail();
        }
        if (isset($options['email_contact_appointment']) && $options['email_contact_appointment']) {
            $recipients['appointment'] = $appt_contact_data['Email'];
        }
        if (empty($recipients)) return true;

        $item_data = $item->getData();
        $item_data['schedule_contact_name']  = $item->getOwnerName();
        $item_data['schedule_contact_email'] = $item->getOwnerEmail();
        $item_data['schedule_start_time_text'] = $item->getTimeText();

        $item_data = array_merge( $item_data, $this->getAppointmentContactData() );

        foreach ($recipients as $which_contact=>$email) {

            $text_id = $options['email_contact_' . $which_contact];
            $text = &new AMPSystem_IntroText( $this->dbcon, $text_id );

            $sch_email = &new AMPSystem_Email();
            $sch_email->setSubject( $text->getTitle() );
            $sch_email->setRecipient( $email );

            $sch_email->setMessage( $text->mergeBodyFields( $item_data ));
            if (!$sch_email->execute()) {
                trigger_error( sprintf( AMP_ERROR_SCHEDULE_APPOINTMENT_EMAIL_NOT_SENT, $email ));
            }
        }

        
        return true;
	}

    function getAppointmentContactData() {

        $result_data = array();
        $appt_contact_data = $this->udm->getData();
        $result_data['appointment_contact_name'] = $appt_contact_data[ 'First_Name'] . " " . $appt_contact_data['Last_Name'];
        $contact_fields = array( 'Phone', 'Email', 'Company' );
        foreach( $contact_fields as $key ) {
            $result_data['appointment_contact_'.$key] = $appt_contact_data[ $key ];
        }
        return $result_data;
    }


}
?>
