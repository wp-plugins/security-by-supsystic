<section class="supsystic-bar">
	<ul class="supsystic-bar-controls">
		<li title="<?php _e('Save all options')?>">
			<button class="button button-primary" id="swrSecureLoginSaveBtn" data-toolbar-button>
				<i class="fa fa-fw fa-save"></i>
				<?php _e('Save', SWR_LANG_CODE)?>
			</button>
		</li>
		<li class="separator">|</li>
		<li title="<?php _e('Your current IP address', SWR_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your current IP address is')?>:
				<span id="swrCurrentIp"><?php echo $this->currentIp?></span>
			</i>
        </li>
	</ul>
	<div style="clear: both;"></div>
	<hr />
</section>
<section>
	<form id="swrSecureLoginForm" class="swrInputsWithDescrForm">
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
							<i class="fa fa-question supsystic-tooltip" title="<?php echo $opt['desc']?>"></i>
						</td>
						<td class="col-w-1perc">
							<?php echo htmlSwr::$htmlType('opt_values['. $optKey. ']', array('value' => $opt['value'], 'attrs' => 'data-optkey="'. $optKey. '"'))?>
						</td>
						<td class="col-w-60perc">
							<div id="swrFormOptDetails_<?php echo $optKey?>" class="swrOptDetailsShell">
							<?php switch($optKey) {
								case 'htaccess_passwd_enable': ?>
								<label>
									<div style="float: left; width: 110px; line-height: 26px;"><?php _e('htaccess login', SWR_LANG_CODE)?>:</div>
									<?php echo htmlSwr::text('htaccess_login');?>
								</label><br />
								<label>
									<div style="float: left; width: 110px; line-height: 26px;"><?php _e('htaccess password', SWR_LANG_CODE)?>:</div>
									<?php echo htmlSwr::text('htaccess_passwd');?>
								</label>
								<?php break;
								case 'login_lockout': ?>
								<label>
									<?php _e('Visitor have', SWR_LANG_CODE)?>
									<?php echo htmlSwr::text('opt_values[login_lockout_attempts]', array('value' => $this->options['login_lockout_attempts']['value'], 'attrs' => 'style="width: 30px;"'));?>
									<?php _e('attempts', SWR_LANG_CODE)?>
								</label>
								<label>
									<?php _e('with time less then', SWR_LANG_CODE)?>
									<?php echo htmlSwr::text('opt_values[login_lockout_stop_time]', array('value' => $this->options['login_lockout_stop_time']['value'], 'attrs' => 'style="width: 30px;"'));?>
									<?php _e('minutes between each try', SWR_LANG_CODE)?>.
								</label>
								<?php _e('Then IP will trap into', SWR_LANG_CODE)?>
								<a href="<?php echo $this->blacklistUrl?>"><?php _e('Blacklist', SWR_LANG_CODE)?></a><br />
								<?php break;
								case 'passwd_min_length_enb': ?>
								<label>
									<?php _e('Set minimal password length to', SWR_LANG_CODE)?>:
									<?php echo htmlSwr::text('opt_values[passwd_min_length]', array('value' => $this->options['passwd_min_length']['value'], 'attrs' => 'style="width: 30px;" data-minvalue="'. $this->options['passwd_min_length']['def']. '"'));?>
								</label><br />
								<i><?php _e('Please be advised that WordPress set minimal password length - to 7 symbols, so you can\'t set less then this value.', SWR_LANG_CODE)?></i>
								<?php break;
								case 'admin_ip_login_enb': ?>
								<a href="#" id="swrAdminIpLoginShowListBtn" class="button"><?php _e('Login IP list', SWR_LANG_CODE)?></a>
								<div id="swrAdminIpLoginCurrentError" class="swrErrorMsg" style="display: none;"><?php _e('Your current IP is not in login list - you will not be able to login from this IP!', SWR_LANG_CODE)?></div>
								<div id="swrAdminIpLoginEmptyError" class="swrErrorMsg" style="display: none;"><?php _e('Available IP list for login is empty - write there IP that you will allow to login for administrators.', SWR_LANG_CODE)?></div>
								<div id="swrAdminIpLoginDialog" style="display: none;" title="<?php _e('Login IP list', SWR_LANG_CODE)?>">
									<label>
										<?php _e('Enter from which IP you want to allow to login, one IP per line')?>:<br />
										<?php echo htmlSwr::textarea('opt_values[admin_ip_login_list]', array('value' => $this->options['admin_ip_login_list']['value'], 'attrs' => 'style="width: 440px; height: 330px;" id="swrAdminLoginIpListTxt"'))?>
									</label>
								</div>
								<?php break;
								case 'admin_pass_change_enb': ?>
								<label>
									<?php _e('Change each', SWR_LANG_CODE)?>
									<?php echo htmlSwr::text('opt_values[admin_pass_change_freq]', array('value' => $this->options['admin_pass_change_freq']['value'], 'attrs' => 'style="width: 40px;"'));?>
									<?php _e('days', SWR_LANG_CODE)?>.
								</label>
								<label>
									<?php _e('Auto change password and send new password - to admin email')?>:
									<?php echo htmlSwr::checkboxHiddenVal('opt_values[admin_pass_change_auto]', array('value' => $this->options['admin_pass_change_auto']['value']));?>
								</label>
								<?php break;
								case 'hide_login_page_enb': ?>
								<label>
									<?php _e('Change login slug (default wp-login.php) to', SWR_LANG_CODE)?>
									<?php echo htmlSwr::text('opt_values[hide_login_page_slug]', array('value' => $this->options['hide_login_page_slug']['value']));?>
								</label>.
								<?php _e('Allowed characters are a-z, 0-9, - and _', SWR_LANG_CODE)?>
								<?php break;
							}?>
							</div>
						</td>
					</tr>
				<?php }?>
			</table>
			<div style="clear: both;"></div>
		</div>
		<?php echo htmlSwr::hidden('mod', array('value' => 'secure_login'))?>
		<?php echo htmlSwr::hidden('action', array('value' => 'saveOptions'))?>
	</form>
	<?php if(!empty($this->simpleAdmins)) { ?>
	<div class="supsystic-item supsystic-panel">
		<h3><?php _e('We scanned your administrators list and found some issues in their accounts, please see list below', SWR_LANG_CODE)?></h3>
		<span class="description"><?php _e('For the broot-force attack it is necessary to choose not only a username but a password as well. The most commonly used usernames are: admin, test, your site’s name or link. That is why malefactors oftentimes select the password for such usernames. By changing the username you’re increasing the safety of your website and its chances of surviving an attack.', SWR_LANG_CODE)?></span>
		<hr>
		<table class="form-table">
			<tr>
				<th class="col-w-30perc"><?php _e('Username', SWR_LANG_CODE)?></th>
				<th><?php _e('Issues', SWR_LANG_CODE)?></th>
			</tr>
			<tbody>
			<?php foreach($this->simpleAdmins as $admin) { ?>
				<tr>
					<td class="col-w-30perc"><?php echo $admin['user']['user_nicename']?></td>
					<td class="alert alert-danger">
						<?php foreach($admin['issues'] as $issue) {?>
							<span style="line-height: 37px; padding-right: 10px;"><?php echo $this->simpleUsersIssues[ $issue ]['label']?></span>
							<a href="<?php echo get_edit_user_link( $admin['user']['ID'] )?>" class="button button-primary"><?php _e('Fix it', SWR_LANG_CODE)?></a>
							<br />
						<?php }?>
					</td>
				</tr>
			<?php }?>
			</tbody>
		</table>
	</div>
	<?php }?>
</section>