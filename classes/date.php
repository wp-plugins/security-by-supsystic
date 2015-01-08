<?php
class dateSwr {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(SWR_DATE_FORMAT_HIS, $time);
	}
}