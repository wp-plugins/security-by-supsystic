<?php
class systemSwr extends moduleSwr {
	private $_contactFormFields = array();
	public function init() {
		parent::init();
		dispatcherSwr::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('System', SWR_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-cog', 'sort_order' => 60,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getContactFormFields() {
		if(empty($this->_contactFormFields)) {
			$this->_contactFormFields = array(
				'name' => array('label' => __('Name', SWR_LANG_CODE), 'validate' => 'notEmpty', 'html' => 'text'),//new fieldSwr('name', __('Your name field is required.', SWR_LANG_CODE), '', '', 'Your name', 0, array(), 'notEmpty'),
				'website' => array('label' => __('Your website', SWR_LANG_CODE), 'validate' => 'notEmpty', 'html' => 'text'),// => new fieldSwr('website', __('Your website field is required.', SWR_LANG_CODE), '', '', 'Your website', 0, array(), 'notEmpty'),
				'email' => array('label' => __('E-mail', SWR_LANG_CODE), 'validate' => 'notEmpty, email', 'html' => 'text'),// => new fieldSwr('email', __('Your e-mail field is required.', SWR_LANG_CODE), '', '', 'Your e-mail', 0, array(), 'notEmpty, email'),
				'subject' => array('label' => __('Subject', SWR_LANG_CODE), 'validate' => 'notEmpty', 'html' => 'text'),// => new fieldSwr('subject', __('Subject field is required.', SWR_LANG_CODE), '', '', 'Subject', 0, array(), 'notEmpty'),
				'category' => array('label' => __('Category', SWR_LANG_CODE), 'validate' => 'notEmpty', 'html' => 'selectbox', 'options' => array()),// => new fieldSwr('category', __('You must select a valid category.', SWR_LANG_CODE), '', '', 'Category', 0, array(), 'notEmpty'),
				'message' => array('label' => __('Message', SWR_LANG_CODE), 'validate' => 'notEmpty', 'html' => 'textarea'),// => new fieldSwr('message', __('Message field is required.', SWR_LANG_CODE), '', '', 'Message', 0, array(), 'notEmpty'),
			);
			$this->_contactFormFields['category']['options'] = array(
				__('- Please select category -', SWR_LANG_CODE),
				__('Plugin options', SWR_LANG_CODE),
				__('Report a bug', SWR_LANG_CODE),
				__('Require new functionality', SWR_LANG_CODE),
				__('Other', SWR_LANG_CODE),
			);
		}
		return $this->_contactFormFields;
	}
}