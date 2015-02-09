<?php
class tableBlacklist_countriesSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__blacklist_countries';
        $this->_id = 'id';
        $this->_alias = 'swr_blacklist_countries';
        $this->_addField('id', 'text', 'int')
				->_addField('country_id', 'text', 'int')
				->_addField('date_created', 'text', 'varchar');
    }
}