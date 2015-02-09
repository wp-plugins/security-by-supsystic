<?php
class modulesModelSwr extends modelSwr {
    public function get($d = array()) {
        if($d['id'] && is_numeric($d['id'])) {
            $fields = frameSwr::_()->getTable('modules')->fillFromDB($d['id'])->getFields();
            $fields['types'] = array();
            $types = frameSwr::_()->getTable('modules_type')->fillFromDB();
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
            }
            return $fields;
        } elseif(!empty($d)) {
            $data = frameSwr::_()->getTable('modules')->get('*', $d);
            return $data;
        } else {
            return frameSwr::_()->getTable('modules')
                ->innerJoin(frameSwr::_()->getTable('modules_type'), 'type_id')
                ->getAll(frameSwr::_()->getTable('modules')->alias().'.*, '. frameSwr::_()->getTable('modules_type')->alias(). '.label as type');
        }
        parent::get($d);
    }
    public function put($d = array()) {
        $res = new responseSwr();
        $id = $this->_getIDFromReq($d);
        $d = prepareParamsSwr($d);
        if(is_numeric($id) && $id) {
            if(isset($d['active']))
                $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;           //mmm.... govnokod?....)))
           /* else
                 $d['active'] = 0;*/
            
            if(frameSwr::_()->getTable('modules')->update($d, array('id' => $id))) {
                $res->messages[] = __('Module Updated', SWR_LANG_CODE);
                $mod = frameSwr::_()->getTable('modules')->getById($id);
                $newType = frameSwr::_()->getTable('modules_type')->getById($mod['type_id'], 'label');
                $newType = $newType['label'];
                $res->data = array(
                    'id' => $id, 
                    'label' => $mod['label'], 
                    'code' => $mod['code'], 
                    'type' => $newType,
                    'params' => utilsSwr::jsonEncode($mod['params']),
                    'description' => $mod['description'],
                    'active' => $mod['active'], 
                );
            } else {
                if($tableErrors = frameSwr::_()->getTable('modules')->getErrors()) {
                    $res->errors = array_merge($res->errors, $tableErrors);
                } else
                    $res->errors[] = __('Module Update Failed', SWR_LANG_CODE);
            }
        } else {
            $res->errors[] = __('Error module ID', SWR_LANG_CODE);
        }
        parent::put($d);
        return $res;
    }
    protected function _getIDFromReq($d = array()) {
        $id = 0;
        if(isset($d['id']))
            $id = $d['id'];
        elseif(isset($d['code'])) {
            $fromDB = $this->get(array('code' => $d['code']));
            if($fromDB[0]['id'])
                $id = $fromDB[0]['id'];
        }
        return $id;
    }
}
