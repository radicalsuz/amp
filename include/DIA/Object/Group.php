<?php

require_once('DIA/Object.php');

class DIA_Groups extends DIA_Object {

    var $_table = 'groups';

	function getGroupsKey() {
		return $this->getProperty('groups_KEY');
	}

	function setGroupsKey($groups_key) {
		return $this->setProperty('groups_KEY', $groups_key);
	}

	function getOrganizationKey() {
		return $this->getProperty('organization_KEY');
	}

	function setOrganizationKey($organization_key) {
		return $this->setProperty('organization_KEY', $organization_key);
	}

	function getChapterKey() {
		return $this->getProperty('chapter_KEY');
	}

	function setChapterKey($chapter_key) {
		return $this->setProperty('chapter_KEY', $chapter_key);
	}

	function getGroupName() {
		return $this->getProperty('Group_Name');
	}

	function setGroupName($group_name) {
		return $this->setProperty('Group_Name', $group_name);
	}

	function getParentKey() {
		return $this->getProperty('parent_KEY');
	}

	function setParentKey($parent_key) {
		return $this->setProperty('parent_KEY', $parent_key);
	}

	function getDescription() {
		return $this->getProperty('Description');
	}

	function setDescription($description) {
		return $this->setProperty('Description', $description);
	}

	function getNotes() {
		return $this->getProperty('Notes');
	}

	function setNotes($notes) {
		return $this->setProperty('Notes', $notes);
	}

	function getDisplayToUser() {
		return $this->getProperty('Display_To_User');
	}

	function setDisplayToUser($display_to_user) {
		return $this->setProperty('Display_To_User', $display_to_user);
	}

	function getListserveType() {
		return $this->getProperty('Listserve_Type');
	}

	function setListserveType($listserve_type) {
		return $this->setProperty('Listserve_Type', $listserve_type);
	}

	function getSubscriptionType() {
		return $this->getProperty('Subscription_Type');
	}

	function setSubscriptionType($subscription_type) {
		return $this->setProperty('Subscription_Type', $subscription_type);
	}

	function getManager() {
		return $this->getProperty('Manager');
	}

	function setManager($manager) {
		return $this->setProperty('Manager', $manager);
	}

	function getModeratorEmails() {
		return $this->getProperty('Moderator_Emails');
	}

	function setModeratorEmails($moderator_emails) {
		return $this->setProperty('Moderator_Emails', $moderator_emails);
	}

	function getSubjectPrefix() {
		return $this->getProperty('Subject_Prefix');
	}

	function setSubjectPrefix($subject_prefix) {
		return $this->setProperty('Subject_Prefix', $subject_prefix);
	}

	function getListserveResponses() {
		return $this->getProperty('Listserve_Responses');
	}

	function setListserveResponses($listserve_responses) {
		return $this->setProperty('Listserve_Responses', $listserve_responses);
	}

	function getAppendHeader() {
		return $this->getProperty('Append_Header');
	}

	function setAppendHeader($append_header) {
		return $this->setProperty('Append_Header', $append_header);
	}

	function getAppendFooter() {
		return $this->getProperty('Append_Footer');
	}

	function setAppendFooter($append_footer) {
		return $this->setProperty('Append_Footer', $append_footer);
	}

	function getCustomHeaders() {
		return $this->getProperty('Custom_Headers');
	}

	function setCustomHeaders($custom_headers) {
		return $this->setProperty('Custom_Headers', $custom_headers);
	}

	function getListserveOptions() {
		return $this->getProperty('Listserve_Options');
	}

	function setListserveOptions($listserve_options) {
		return $this->setProperty('Listserve_Options', $listserve_options);
	}

	function getExternalId() {
		return $this->getProperty('external_ID');
	}

	function setExternalId($external_id) {
		return $this->setProperty('external_ID', $external_id);
	}

	function getFrom() {
		return $this->getProperty('_From');
	}

	function setFrom($_from) {
		return $this->setProperty('_From', $_from);
	}

	function getFromName() {
		return $this->getProperty('From_Name');
	}

	function setFromName($from_name) {
		return $this->setProperty('From_Name', $from_name);
	}

	function getReplyTo() {
		return $this->getProperty('Reply_To');
	}

	function setReplyTo($reply_to) {
		return $this->setProperty('Reply_To', $reply_to);
	}

	function getHeadersToRemove() {
		return $this->getProperty('Headers_To_Remove');
	}

	function setHeadersToRemove($headers_to_remove) {
		return $this->setProperty('Headers_To_Remove', $headers_to_remove);
	}

	function getConfirmationMessage() {
		return $this->getProperty('Confirmation_Message');
	}

	function setConfirmationMessage($confirmation_message) {
		return $this->setProperty('Confirmation_Message', $confirmation_message);
	}

}

?>