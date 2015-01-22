<section class="supsystic-bar">
	<ul class="supsystic-bar-controls">
		<li title="<?php _e('Save all options')?>">
			<button class="button button-primary" id="swrSecureHideSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', SWR_LANG_CODE)?>
			</button>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="swrSecureHideForm" class="swrInputsWithDescrForm">
		<div class="supsystic-item supsystic-panel">
			<table class="form-table">
				<?php foreach($this->options as $optKey => $opt) { ?>
					<?php
						$htmlType = isset($opt['html']) ? $opt['html'] : false;
						if(empty($htmlType)) continue;
					?>
					<tr>
						<th scope="row" class="col-w-30perc">
							<?php echo $opt['label']?>
							<?php if(!empty($opt['changed_on'])) {?>
								<br />
								<span class="description">
									<?php 
									$opt['value'] 
										? printf(__('Turned On %s', SWR_LANG_CODE), dateSwr::_($opt['changed_on']))
										: printf(__('Turned Off %s', SWR_LANG_CODE), dateSwr::_($opt['changed_on']))
									?>
								</span>
							<?php }?>
						</th>
						<td class="col-w-1perc">
							<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html($opt['desc'])?>"></i>
						</td>
						<td class="col-w-1perc">
							<?php echo htmlSwr::$htmlType('opt_values['. $optKey. ']', array('value' => $opt['value'], 'attrs' => 'data-optkey="'. $optKey. '"'))?>
						</td>
						<td class="col-w-50perc">
							<div id="swrFormOptDetails_<?php echo $optKey?>" class="swrOptDetailsShell">
							<?php switch($optKey) {
							case 'change_standard_db_pref_enb': ?>
								<?php if($this->options['old_db_pref']['value'] != 'wp_') {
									_e('You have not standard WordPress prefix for now', SWR_LANG_CODE);
								}?>
								<?php break;
							}
							?>
							</div>
						</td>
					</tr>
				<?php }?>
			</table>
			<div style="clear: both;"></div>
		</div>
		<?php echo htmlSwr::hidden('mod', array('value' => 'secure_hide'))?>
		<?php echo htmlSwr::hidden('action', array('value' => 'saveOptions'))?>
	</form>
</section>