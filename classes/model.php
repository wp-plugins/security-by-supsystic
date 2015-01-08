<?php
abstract class modelSwr extends baseObjectSwr {
    protected $_data = array();
	protected $_code = '';
    
	protected $_orderBy = '';
	protected $_sortOrder = '';
	protected $_limit = '';
	protected $_where = array();
	protected $_stringWhere = '';
	protected $_selectFields = '*';
	protected $_tbl = '';
	protected $_lastGetCount = 0;
	
    public function init() {

    }
    public function get($d = array()) {

    }
    public function put($d = array()) {

    }
    public function post($d = array()) {

    }
    public function delete($d = array()) {

    }
    public function store($d = array()) {
        
    }
	public function setCode($code) {
        $this->_code = $code;
    }
    public function getCode() {
        return $this->_code;
    }
	public function getModule() {
		return frameSwr::_()->getModule( $this->_code );
	}
	
	protected function _setTbl($tbl) {
		$this->_tbl = $tbl;
	}	
	public function setOrderBy($orderBy) {
		$this->_orderBy = $orderBy;
		return $this;
	}
	/**
	 * ASC, DESC
	 */
	public function setSortOrder($sortOrder) {
		$this->_sortOrder = $sortOrder;
		return $this;
	}
	public function setLimit($limit) {
		$this->_limit = $limit;
		return $this;
	}
	public function setWhere($where) {
		$this->_where = $where;
		return $this;
	}
	public function addWhere($where) {
		if(empty($this->_where)) {
			$this->setWhere( $where );
		} elseif(is_array($this->_where) && is_array($where)) {
			$this->_where = array_merge($this->_where, $where);
		} elseif(is_string($this->_where) && is_string($where)) {
			$this->_stringWhere .= $where;	// Unused for now
		}
		return $this;
	}
	public function setSelectFields($selectFields) {
		$this->_selectFields = $selectFields;
		return $this;
	}
	public function getLastGetCount() {
		return $this->_lastGetCount;
	}
	public function getFromTbl($params = array()) {
		$this->_lastGetCount = 0;
		$tbl = isset($params['tbl']) ? $params['tbl'] : $this->_tbl;
		$table = frameSwr::_()->getTable( $tbl );
		$this->_buildQuery( $table );
		$data = $table->get($this->_selectFields, $this->_where);
		if(!empty($data)) {
			foreach($data as $i => $row) {
				$data[ $i ] = $this->_afterGetFromTbl( $row );
			}
			$this->_lastGetCount = count( $data );
		}
		$this->_clearQuery( $params );
		return $data;
	}
	protected function _clearQuery($params = array()) {
		$clear = isset($params['clear']) ? $params['clear'] : array();
		if(!is_array($clear))
			$clear = array($clear);
		if(empty($clear) || in_array('limit', $clear))
			$this->_limit = '';
		if(empty($clear) || in_array('orderBy', $clear))
			$this->_orderBy = '';
		if(empty($clear) || in_array('sortOrder', $clear))
			$this->_sortOrder = '';
		if(empty($clear) || in_array('where', $clear))
			$this->_where = '';
		if(empty($clear) || in_array('selectFields', $clear))
			$this->_selectFields = '*';
	}
	public function getCount($params = array()) {
		$tbl = isset($params['tbl']) ? $params['tbl'] : $this->_tbl;
		$table = frameSwr::_()->getTable( $tbl );
		$this->setSelectFields('COUNT(*) AS total');
		$this->_buildQuery( $table );
		$data = (int) $table->get($this->_selectFields, $this->_where, '', 'one');
		$this->_clearQuery($params);
		return $data;
	}
	protected function _afterGetFromTbl( $row ) {	// You can re-define this method in your own model
		return $row;
	}
	protected function _buildQuery($table = null) {
		if(!$table)
			$table = frameSwr::_()->getTable( $this->_tbl );
		if(!empty($this->_orderBy)) {
			$order = $this->_orderBy;
			if(!empty($this->_sortOrder))
				$order .= ' '. strtoupper($this->_sortOrder);
			$table->orderBy( $order );
			
		}
		if(!empty($this->_limit)) {
			$table->setLimit( $this->_limit );
		}
	}
	public function removeGroup($ids) {
		if(!is_array($ids))
			$ids = array($ids);
		// Remove all empty values
		$ids = array_filter(array_map('intval', $ids));
		if(!empty($ids)) {
			if(frameSwr::_()->getTable( $this->_tbl )->delete(array('additionalCondition' => 'id IN ('. implode(',', $ids). ')'))) {
				return true;
			} else 
				$this->pushError (__('Database error detected', SWR_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', SWR_LANG_CODE));
		return false;
	}
	public function clear() {
		if(frameSwr::_()->getTable( $this->_tbl )->delete()) {
			return true;
		} else 
			$this->pushError (__('Database error detected', SWR_LANG_CODE));
		return false;
	}
}
