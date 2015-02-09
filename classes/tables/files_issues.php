<?php
class tableFiles_issuesSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__files_issues';
        $this->_id = 'id';
        $this->_alias = 'swr_files_issues';
        $this->_addField('id', 'text', 'int')
				->_addField('filepathMd5', 'text', 'varchar')
				->_addField('filepath', 'text', 'varchar')
				->_addField('filename', 'text', 'varchar')
				->_addField('last_time_modified', 'text', 'varchar')
				->_addField('type', 'text', 'varchar')
				->_addField('location_type', 'text', 'varchar')
				->_addField('last_scan', 'text', 'varchar')
				->_addField('date_found', 'text', 'varchar');
    }
}
