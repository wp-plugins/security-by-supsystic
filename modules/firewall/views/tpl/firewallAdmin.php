<section class="supsystic-bar">
	<ul class="supsystic-bar-controls">
		<li title="<?php _e('Save all options', SWR_LANG_CODE)?>">
			<button class="button button-primary" id="swrFirewallSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', SWR_LANG_CODE)?>
			</button>
		</li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="swrFirewallForm" class="swrInputsWithDescrForm">
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
								case '404_black_list_detection': ?>
								<label>
									<?php _e('Visitor can visit 404 page', SWR_LANG_CODE)?>
									<?php echo htmlSwr::text('opt_values[404_bld_attempts]', array('value' => $this->options['404_bld_attempts']['value'], 'attrs' => 'style="width: 30px;"'));?>
									<?php _e('times', SWR_LANG_CODE)?>
								</label>
								<label>
									<?php _e('with time less then', SWR_LANG_CODE)?>
									<?php echo htmlSwr::text('opt_values[404_bld_stop_time]', array('value' => $this->options['404_bld_stop_time']['value'], 'attrs' => 'style="width: 30px;"'));?>
									<?php _e('minutes between each try visit', SWR_LANG_CODE)?>.
								</label>
								<?php _e('Then IP will trap into', SWR_LANG_CODE)?>
								<a href="<?php echo $this->blacklistUrl?>"><?php _e('Blacklist', SWR_LANG_CODE)?></a><br />
								<?php break;
							}?>
							</div>
						</td>
					</tr>
				<?php }?>
			</table>
			<div style="clear: both;"></div>
		</div>
		<?php echo htmlSwr::hidden('mod', array('value' => 'firewall'))?>
		<?php echo htmlSwr::hidden('action', array('value' => 'saveOptions'))?>
	</form>
</section>