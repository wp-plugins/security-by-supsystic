<?php
class tableFiles_snapshotSwr extends tableSwr {
    public function __construct() {
        $this->_table = '@__files_snapshot';
        $this->_alias = 'toe_files_snapshot';
        $this->_addField('filepathMd5', 'text', 'varchar')
				->_addField('filepathMd5', 'text', 'varchar')
				->_addField('filepath', 'text', 'varchar')
				->_addField('md5', 'text', 'varchar')
				->_addField('md5_old', 'text', 'varchar')
				->_addField('version', 'text', 'varchar');
    }
}
