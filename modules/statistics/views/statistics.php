<?php
class statisticsViewSwr extends viewSwr {
	public function getTabContent() {
		frameSwr::_()->getModule('templates')->loadJqplot();
		
		$statsTab = $this->getModule()->getCurrentStatTab();
		switch($statsTab) {
			case 'login':
				$requests = $this->getModel()->getGraphLogin();
				break;
			case '404':
				$requests = $this->getModel()->getGraph404();
				break;
			case 'all':
			default:
				$requests = $this->getModel()->getGraphAll();
				$statsTab = 'all';
				break;
		}
		
		frameSwr::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		frameSwr::_()->addJSVar('admin.'. $this->getCode(), 'swrStatRequests', $requests);
		
		$haveData = $requests['graph'] 
				&& !empty($requests['graph']) 
				&& isset($requests['graph'][0]) 
				&& !empty($requests['graph'][0]) 
				&& isset($requests['most_visited_url']['total_requests']) 
				&& !empty($requests['most_visited_url']['total_requests']);
		
		$this->assign('requests', $requests);
		$this->assign('currentStatsTab', $statsTab);
		$this->assign('statsTabs', $this->getModule()->getStatTabs());
		$this->assign('haveData', $haveData);
		return parent::getContent('statisticsAdmin');
	}
}
