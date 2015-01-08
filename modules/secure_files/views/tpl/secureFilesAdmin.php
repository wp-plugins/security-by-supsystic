<style type="text/css">
	#swrFileScanChangeProgressRow,
	#swrFileScanPermsProgressRow {
		display: none;
	}
</style>
<section class="supsystic-bar">
	<h4><?php _e('Access to your files - is a direct way to access your site. Check your files security now.', SWR_LANG_CODE)?></h4>
</section>
<section>
	<div class="supsystic-item supsystic-panel">
		<table class="form-table">
			<!--Changes scan-->
			<tr>
				<th scope="row" class="col-w-40perc">
					<?php echo $this->options['last_time_files_change_check']['label']?><br />
					<span class="description">
						<?php 
						if(empty($this->options['last_time_files_change_check']['value'])) {
							_e('Start your first scan - and you will see results');
						} else {
							printf(__('Last Scan %s'), dateSwr::_($this->options['last_time_files_change_check']['value']));
						}
						?>
					</span>
				</th>
				<td class="col-w-1perc">
					<i class="fa fa-question supsystic-tooltip" title="<?php echo $this->options['last_time_files_change_check']['desc']?>"></i>
				</td>
				<td class="col-w-20perc" style="width: 20px;">
					<button id="swrFileScanChangeBtn" 
							class="button button-primary" 
							data-loadtext="<?php _e('Scan in progress', SWR_LANG_CODE)?>"
							data-text="<?php _e('Scan Now', SWR_LANG_CODE)?>">
						<span class="swrBtnInnerTxt"><?php _e('Scan Now', SWR_LANG_CODE)?></span>
						<i class="fa fa-fw fa-level-up"></i>
					</button>
				</td>
				<td class="col-w-30perc">
					<?php
						$alertClass = empty($this->alerts['last_time_files_change_check']) ? 'success' : $this->alerts['last_time_files_change_check']['alert_class'];
						$alertMsg = empty($this->alerts['last_time_files_change_check']) ? __('No issues found', SWR_LANG_CODE) : $this->alerts['last_time_files_change_check']['desc'];
					?>
					<div class="alert alert-<?php echo $alertClass?>"><?php echo $alertMsg?></div>
				</td>
			</tr>
			<tr id="swrFileScanChangeProgressRow">
				<th scope="row" colspan="4" style="width: 100%;">
					<div style="width: 100%; height: 28px; line-height: 28px;" 
						 data-finished="<?php _e('Saving results...', SWR_LANG_CODE)?>" 
						 class="progress-button" 
						 id="swrFileScanChangeProgressLine"></div>
				</th>
			</tr>
			<!--Permissions scan-->
			<tr>
				<th scope="row" class="col-w-40perc">
					<?php echo $this->options['last_time_files_perms_check']['label']?><br />
					<span class="description">
						<?php 
						if(empty($this->options['last_time_files_perms_check']['value'])) {
							_e('Start your first scan - and you will see results', SWR_LANG_CODE);
						} else {
							printf(__('Last Scan %s', SWR_LANG_CODE), dateSwr::_($this->options['last_time_files_perms_check']['value']));
						}
						?>
					</span>
				</th>
				<td class="col-w-1perc">
					<i class="fa fa-question supsystic-tooltip" title="<?php echo $this->options['last_time_files_perms_check']['desc']?>"></i>
				</td>
				<td class="col-w-20perc" style="width: 20px;">
					<button id="swrFileScanPermsBtn" 
							class="button button-primary" 
							data-loadtext="<?php _e('Scan in progress', SWR_LANG_CODE)?>"
							data-text="<?php _e('Scan Now', SWR_LANG_CODE)?>">
						<span class="swrBtnInnerTxt"><?php _e('Scan Now', SWR_LANG_CODE)?></span>
						<i class="fa fa-fw fa-level-up"></i>
					</button>
				</td>
				<td class="col-w-30perc">
					<?php
						$alertClass = empty($this->alerts['last_time_files_perms_check']) ? 'success' : $this->alerts['last_time_files_perms_check']['alert_class'];
						$alertMsg = empty($this->alerts['last_time_files_perms_check']) ? __('No issues found', SWR_LANG_CODE) : $this->alerts['last_time_files_perms_check']['desc'];
					?>
					<div class="alert alert-<?php echo $alertClass?>"><?php echo $alertMsg?></div>
				</td>
			</tr>
			<tr id="swrFileScanPermsProgressRow">
				<th scope="row" colspan="4" style="width: 100%;">
					<div style="width: 100%; height: 28px; line-height: 28px;" 
						 data-finished="<?php _e('Saving results...', SWR_LANG_CODE)?>" 
						 class="progress-button" 
						 id="swrFileScanPermsProgressLine"></div>
				</th>
			</tr>
		</table>
		<div style="clear: both;"></div>

		<ul class="supsystic-bar-controls">
			<li title="<?php _e('Delete selected', SWR_LANG_CODE)?>">
				<button class="button" id="swrFilesIssuesRemoveGroupBtn" disabled data-toolbar-button>
					<i class="fa fa-fw fa-trash-o"></i>
					<?php _e('Delete selected', SWR_LANG_CODE)?>
				</button>
			</li>
			<li title="<?php _e('Clear All', SWR_LANG_CODE)?>">
				<button class="button" id="swrFilesIssuesClearBtn" disabled data-toolbar-button>
					<?php _e('Clear List', SWR_LANG_CODE)?>
				</button>
			</li>
			<li title="<?php _e('Search', SWR_LANG_CODE)?>">
				<input id="swrFilesTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', SWR_LANG_CODE)?>">
			</li>
		</ul>
		<div id="swrFilesIssuesTblNavShell" class="supsystic-tbl-pagination-shell"></div>
		<?php echo htmlSwr::selectbox('search_types', array('options' => $this->typesForSelect, 'attrs' => 'id="swrFilesIssuesTypeSel" onchange="swrFilesIssueTypeSelChange();" class="supsystic-no-customize"', 'value' => $this->typeSelected))?>
		<div style="clear: both;"></div>
		<hr />
		
		<table id="swrFilesIssuesTbl"></table>
		<div id="swrFilesIssuesTblNav"></div>
		<div id="swrFilesIssuesTblEmptyMsg" style="display: none;">
			<h3><?php _e('No data found', SWR_LANG_CODE)?></h3>
		</div>
	</div>
</section>