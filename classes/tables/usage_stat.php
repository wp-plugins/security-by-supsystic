<?php
class tableUsage_statSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__usage_stat';
        $this->_id = 'id';     
        $this->_alias = 'toe_usage_stat';
        $this->_addField('id', 'hidden', 'int', 0, __('id', SWR_LANG_CODE))
			->_addField('code', 'hidden', 'text', 0, __('code', SWR_LANG_CODE))
			->_addField('visits', 'hidden', 'int', 0, __('visits', SWR_LANG_CODE))
			->_addField('spent_time', 'hidden', 'int', 0, __('spent_time', SWR_LANG_CODE))
			->_addField('modify_timestamp', 'hidden', 'int', 0, __('modify_timestamp', SWR_LANG_CODE));
    }
}