<?php
class pagesViewSwr extends viewSwr {
    public function displayDeactivatePage() {
        $this->assign('GET', reqSwr::get('get'));
        $this->assign('POST', reqSwr::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqSwr::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqSwr::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

