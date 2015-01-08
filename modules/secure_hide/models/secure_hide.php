<?php
class secure_hideModelSwr extends modelSwr {
	public function afterOptionsChange($prevOptsModel, $optsModel, $submitData) {
		// WordPress tables pref. change
		$prevOpt = $prevOptsModel->get('change_standard_db_pref_enb');
		$currOpt = $optsModel->get('change_standard_db_pref_enb');
		global $wpdb;

		if($currOpt && !$prevOpt) {
			$newPref = 'wp_'. strtolower(utilsSwr::getRandStr(4)). '_';
			$optsModel->save('old_db_pref', $wpdb->prefix);
			//var_dump($newPref, $optsModel->get('old_db_pref')); exit();
			$optsModel->save('new_db_pref', $newPref);
			$this->changeDbPref($optsModel->get('old_db_pref'), $newPref);
			$wpdb->prefix = $newPref;
		} elseif($prevOpt && !$currOpt) {
			$this->changeDbPref($optsModel->get('new_db_pref'), $optsModel->get('old_db_pref'));
		}
		return !$this->haveErrors();
	}
	public function changeDbPref($fromPref, $toPref) {
		$configFilePath = ABSPATH. 'wp-config.php';
		if(utilsSwr::isWritable($configFilePath)) {
			if(!empty($fromPref) && !empty($toPref)) {
				file_put_contents($configFilePath, 
					preg_replace('/table_prefix\s*\=\s*\''. $fromPref. '\'\s*;/', 'table_prefix = \''. $toPref. '\';', 
						file_get_contents($configFilePath)));
				$tablesList = dbSwr::get('SHOW TABLES', 'col');
				foreach($tablesList as $tbl) {
					dbSwr::query('RENAME TABLE '. $tbl. ' TO '. str_replace($fromPref, $toPref, $tbl));
				}
				dbSwr::query("UPDATE IGNORE `". $toPref. "options` SET `option_name`=REPLACE(`option_name`,'$fromPref','$toPref') WHERE `option_name` LIKE '%$fromPref%'");
				dbSwr::query("UPDATE IGNORE `". $toPref. "usermeta` SET `meta_key`=REPLACE(`meta_key`,'$fromPref','$toPref') WHERE `meta_key` LIKE '%$fromPref%'");
			} else
				$this->pushError (__('Can\'t detect current database prefix', SWR_LANG_CODE));
		} else
			$this->pushError (sprintf(__('Your %s file is not writable - can\'t change database prefix', SWR_LANG_CODE), $configFilePath));
		return $configFilePath;
	}
}
