<?php
	$title = SWR_WP_PLUGIN_NAME;
?>
<html>
    <head>
        <title><?php langSwr::_e( $title )?></title>
    </head>
    <body>
<div style="position: fixed; margin-left: 40%; margin-right: auto; text-align: center; background-color: #fdf5ce; padding: 10px; margin-top: 10%;">
    <div><?php langSwr::_e( $title )?></div>
    <?php echo htmlSwr::formStart('deactivatePlugin', array('action' => $this->REQUEST_URI, 'method' => $this->REQUEST_METHOD))?>
    <?php
        $formData = array();
        switch($this->REQUEST_METHOD) {
            case 'GET':
                $formData = $this->GET;
                break;
            case 'POST':
                $formData = $this->POST;
                break;
        }
        foreach($formData as $key => $val) {
            if(is_array($val)) {
                foreach($val as $subKey => $subVal) {
                    echo htmlSwr::hidden($key. '['. $subKey. ']', array('value' => $subVal));
                }
            } else
                echo htmlSwr::hidden($key, array('value' => $val));
        }
    ?>
        <table width="100%">
            <tr>
                <td><?php _e('Delete Plugin Data (options, setup data, database tables, etc.)', SWR_LANG_CODE)?>:</td>
                <td><?php echo htmlSwr::radiobuttons('deleteOptions', array('options' => array('No', 'Yes')))?></td>
            </tr>
        </table>
    <?php echo htmlSwr::submit('toeGo', array('value' => __('Done', SWR_LANG_CODE)))?>
    <?php echo htmlSwr::formEnd()?>
    </div>
</body>
</html>