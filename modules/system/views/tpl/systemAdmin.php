<section>
	<div id="containerWrapper">
		<div class="supsystic-item supsystic-panel">
			<h3><?php _e('Mail settings', SWR_LANG_CODE)?></h3>
			<hr /><div style="clear: both;"></div>
			<?php echo $this->mailContent?>
		</div>
		<div class="supsystic-item supsystic-panel" id="swrHtaccessBlock">
			<h3><?php _e('.htaccess settings', SWR_LANG_CODE)?></h3>
			<hr /><div style="clear: both;"></div>
			<?php echo $this->htaccessContent?>
		</div>
		<div class="supsystic-item supsystic-panel">
			<h3><?php _e('Plugin usage statistics', SWR_LANG_CODE)?></h3>
			<hr /><div style="clear: both;"></div>
			<form id="swrPlugUsageStatForm">
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php _e('Allow plugin to send anonymous usage statistics', SWR_LANG_CODE)?>:
						</th>
						<td>
							<?php echo htmlSwr::selectbox('opt_values[send_stats]', array('value' => $this->sendStatistic, 'options' => array('1' => __('Yes', SWR_LANG_CODE), '0' => __('No', SWR_LANG_CODE))))?><br />
							<p class="description">
								<?php _e('We don\'t collect any of your private information, this is only statistics of our plugin usage - to detect your priorities on its usage and make it better for You.')?>
							</p>
						</td>
					</tr>
				</table>
				<?php echo htmlSwr::hidden('mod', array('value' => 'options'))?>
				<?php echo htmlSwr::hidden('action', array('value' => 'saveGroup'))?>
				<button class="button button-primary">
					<i class="fa fa-fw fa-save"></i>
					<?php _e('Save', SWR_LANG_CODE)?>
				</button>
			</form>
		</div>
		<div class="supsystic-item supsystic-panel">
			<h3><?php _e('System info', SWR_LANG_CODE)?></h3>
			<hr /><div style="clear: both;"></div>
			<table>
				<?php foreach($this->systemInfo as $label => $data) { ?>
				<tr>
					<th scope="row" align="left"><?php echo $label?>:</th>
					<td><?php echo $data['value']?></td>
				</tr>
				<?php }?>
			</table>
		</div>
		<div class="supsystic-item supsystic-panel">
			<h3><?php _e('Contact us', SWR_LANG_CODE)?></h3>
			<hr />
			<p class="description">
				<?php _e('If you have any question, or want to report about a bug - just contact us using form below.', SWR_LANG_CODE)?>
			</p>
			<div style="clear: both;"></div>
			<form id="swrContactForm">
				<?php foreach($this->contactFormFields as $fName => $fData) {
					$htmlParams = array();
					if(isset($fData['def']))
						$htmlParams['value'] = $fData['def'];
					if(isset($fData['options']))
						$htmlParams['options'] = $fData['options'];
					$htmlType = $fData['html'];
					$id = 'swrContactFormField_'. $fName;
					$htmlParams['attrs'] = 'id="'. $id. '" placeholder="'. $fData['label']. '"';
				?>
				<?php echo htmlSwr::$htmlType($fName, $htmlParams); ?><br />
				
				<?php }?>
				<?php echo htmlSwr::hidden('mod', array('value' => 'system'));?>
				<?php echo htmlSwr::hidden('action', array('value' => 'sendContact'));?>
				<button class="button button-primary">
					<i class="fa fa-paper-plane"></i>
					<?php _e('Send', SWR_LANG_CODE)?>
				</button>
			</form>
			<div id="swrContactThankyou" class="alert alert-success" style="display: none;">
				<?php _e('Thank you for contacting us! We will respond you as soon as possible.', SWR_LANG_CODE)?>
			</div>
		</div>
	</div>
</section>
