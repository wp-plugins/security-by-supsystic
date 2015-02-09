<?php
class statisticsViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->getModule('templates')->loadJqplot();
		$haveData = false;
		$statsTab = $this->getModule()->getCurrentStatTab();
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->addJSVar('admin.'. $this->getCode(), 'swrCurrentStatsTab', $statsTab);
		switch($statsTab) {
			case 'login':
				$requests = $this->getModel()->getGraphLogin();
				break;
			case '404':
				$requests = $this->getModel()->getGraph404();
				break;
			case 'detailed_login':
				frameSwr::_()->getModule('templates')->loadJqGrid();
				frameSwr::_()->addJSVar('admin.'. $this->getCode(), 'swrDetailedLoginDataUrl', uriSwr::mod('statistics', 'getListForTblDetailedLogin', array('reqType' => 'ajax')));
				$haveData = true;
				break;
			case 'all':
			default:
				$requests = $this->getModel()->getGraphAll();
				$statsTab = 'all';
				break;
		}
		if(isset($requests)) {
			frameSwr::_()->addJSVar('admin.'. $this->getCode(), 'swrStatRequests', $requests);
			$haveData = $requests['graph'] 
				&& !empty($requests['graph']) 
				&& isset($requests['graph'][0]) 
				&& !empty($requests['graph'][0]) 
				&& isset($requests['most_visited_url']['total_requests']) 
				&& !empty($requests['most_visited_url']['total_requests']);
			$this->assign('requests', $requests);
		}
		
		$this->assign('haveData', $haveData);
		$this->assign('currentStatsTab', $statsTab);
		$this->assign('statsTabs', $this->getModule()->getStatTabs());
		
		return parent::getContent('statisticsAdmin');
	}
}
