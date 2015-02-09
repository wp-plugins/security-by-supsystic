<?php
class tableBlacklist_browsersSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__blacklist_browsers';
        $this->_id = 'id';
        $this->_alias = 'swr_blacklist_browsers';
        $this->_addField('id', 'text', 'int')
				->_addField('browser_name', 'text', 'varchar')
				->_addField('date_created', 'text', 'varchar');
    }
}