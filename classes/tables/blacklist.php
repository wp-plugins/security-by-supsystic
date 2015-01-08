<?php
class tableBlacklistSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__blacklist';
        $this->_id = 'id';
        $this->_alias = 'toe_blacklist';
        $this->_addField('id', 'text', 'int')
				->_addField('ip', 'text', 'varchar')
				->_addField('type', 'text', 'int')
				->_addField('date_created', 'text', 'varchar');
    }
}
