<?php
class statisticsModelSwr extends modelSwr {
	public function insert($d = array()) {
		$d['url'] = isset($d['url']) ? $d['url'] : '';
		$d['url'] = dbSwr::prepareHtml($d['url']);
		return frameSwr::_()->getTable('statistics')->insert($d);
	}
	public function updateType($id, $type) {
		return frameSwr::_()->getTable('statistics')->update(array(
			'type' => $type,
		), array(
			'id' => $id,
		));
	}
	public function getGraphAll() {
		return array(
			'graph' => array(0 => array('points' => $this->getGraph())),
			'most_visited_url' => $this->getMostVisitedUrl(),
			'most_active_ip' => $this->getMostActiveIp(),
		);
	}
	public function getGraph404() {
		$condition = array('type' => $this->getModule()->getTypeId('404'));
		return array(
			'graph' => array(0 => array('points' => $this->getGraph( $condition ))),
			'most_visited_url' => $this->getMostVisitedUrl( $condition ),
			'most_active_ip' => $this->getMostActiveIp( $condition ),
		);
	}
	public function getGraphLogin() {
		$condition = array('additionalCondition' => 'type IN ('
			. $this->getModule()->getTypeId('login')
			. ', '. $this->getModule()->getTypeId('login_submit')
			. ', '. $this->getModule()->getTypeId('login_error'). ')');
		return array(
			'graph' => array(
				0 => array('points' => $this->getGraph( array('type' => $this->getModule()->getTypeId('login')) ), 'label' => __('Login page visits', SWR_LANG_CODE)),
				1 => array('points' => $this->getGraph( array('type' => $this->getModule()->getTypeId('login_submit')) ), 'label' => __('Login form submits', SWR_LANG_CODE)),
				2 => array('points' => $this->getGraph( array('type' => $this->getModule()->getTypeId('login_error')) ), 'label' => __('Login submit errors', SWR_LANG_CODE)),
			),
			'most_visited_url' => $this->getMostVisitedUrl( $condition ),
			'most_active_ip' => $this->getMostActiveIp( $condition ),
		);
	}
	public function getGraph($d = array()) {
		return frameSwr::_()->getTable('statistics')
				->groupBy('date')
				->orderBy('date_created DESC')
				->get('COUNT(*) AS total_requests, DATE_FORMAT(date_created, "%m-%d-%Y") AS date', $d);
	}
	public function getMostVisitedUrl($d = array()) {
		return frameSwr::_()->getTable('statistics')
				->groupBy('url')
				->limit(1)
				->orderBy('total_requests DESC')
				->get('COUNT(*) AS total_requests, url', $d, '', 'row');
	}
	public function getMostActiveIp($d = array()) {
		return frameSwr::_()->getTable('statistics')
				->groupBy('ip')
				->limit(1)
				->orderBy('ip DESC')
				->get('COUNT(*) AS total_requests, ip', $d, '', 'row');
	}
	public function clear($tab) {
		$condition = array();
		switch($tab) {
			case 'login':
				$condition = array('additionalCondition' => 'type IN ('
					. $this->getModule()->getTypeId('login')
					. ', '. $this->getModule()->getTypeId('login_submit')
					. ', '. $this->getModule()->getTypeId('login_error'). ')');
				break;
			case '404':
				$condition = array('type' => $this->getModule()->getTypeId('404'));
				break;
		}
		return frameSwr::_()->getTable('statistics')->delete($condition);
	}
}