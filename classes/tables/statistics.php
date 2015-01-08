<?php
class tableStatisticsSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__statistics';
        $this->_id = 'id';
        $this->_alias = 'toe_statistics';
        $this->_addField('id', 'hidden', 'int')
			->_addField('ip', 'text', 'varchar')
			->_addField('type', 'text', 'int')
			->_addField('url', 'text', 'varchar')
			->_addField('date_created', 'text', 'varchar');
    }
}