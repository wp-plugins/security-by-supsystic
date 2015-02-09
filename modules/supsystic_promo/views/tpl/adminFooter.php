<div class="swrAdminFooterShell">
	<div class="swrAdminFooterCell">
		<?php echo SWR_WP_PLUGIN_NAME?>
		<?php _e('Version', SWR_LANG_CODE)?>:
		<a target="_blank" href="http://wordpress.org//plugins/supsystic-secure/changelog/"><?php echo SWR_VERSION?></a>
	</div>
	<div class="swrAdminFooterCell">|</div>
	<?php if(!frameSwr::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="swrAdminFooterCell">
		<?php _e('Go', SWR_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo $this->getModule()->preparePromoLink('http://supsystic.com/product/supsystic-secure/');?>"><?php _e('PRO', SWR_LANG_CODE)?></a>
	</div>
	<div class="swrAdminFooterCell">|</div>
	<?php }?>
	<div class="swrAdminFooterCell">
		<a target="_blank" href="http://wordpress.org//support/plugin/supsystic-secure"><?php _e('Support', SWR_LANG_CODE)?></a>
	</div>
	<div class="swrAdminFooterCell">|</div>
	<div class="swrAdminFooterCell">
		Add your <a target="_blank" href="http://wordpress.org//support/view/plugin-reviews/supsystic-secure?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on wordpress.org.
	</div>
</div>