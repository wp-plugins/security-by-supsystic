<section class="supsystic-bar">
    <ul class="supsystic-bar-controls">
        <li title="<?php _e('Add IP to blacklist', SWR_LANG_CODE)?>">
            <button class="button button-primary swrBlacklistAddByIpBtn">
                <i class="fa fa-fw fa-plus"></i>
                <?php _e('Add by IP', SWR_LANG_CODE)?>
            </button>
			<button class="button button-primary swrBlacklistAddByCountryBtn">
                <i class="fa fa-fw fa-plus"></i>
                <?php _e('Add by Country', SWR_LANG_CODE)?>
            </button>
			<button class="button button-primary swrBlacklistAddByBrowserBtn">
                <i class="fa fa-fw fa-plus"></i>
                <?php _e('Add by Browser', SWR_LANG_CODE)?>
            </button>
        </li>
		<div style="clear: both;"></div>
		<li title="<?php _e('Your current IP address', SWR_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your current IP address is', SWR_LANG_CODE)?>:
				<span id="swrCurrentIp"><?php echo $this->currentIp?></span>
			</i>
        </li>
		<li class="separator">|</li>
		<li title="<?php _e('Country', SWR_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your Country', SWR_LANG_CODE)?>:
				<span id="swrCurrentCountryCode"><?php echo (empty($this->currentCountry) ? __('not detected', SWR_LANG_CODE) : $this->currentCountry)?></span>
				<span id="swrBlockedCountriesMsg" style="display: none;">
					<?php _e('Site is blocked for <span id="swrBlockedCountriesCount" class="swrErrorMsg">%d</span> countries. For more info - click <a href="" class="swrBlacklistAddByCountryBtn">Add by Country</a> button', SWR_LANG_CODE)?>
				</span>
			</i>
        </li>
		<li class="separator">|</li>
		<li title="<?php _e('Browser', SWR_LANG_CODE)?>">
			<i style="display: block; margin-top: 5px;">
				<?php _e('Your Browser', SWR_LANG_CODE)?>:
				<span id="swrCurrentBrowserName"><?php echo $this->currentBrowser['name']?></span>
				<span id="swrBlockedBrowsersMsg" style="display: none;">
					<?php _e('Site is blocked for <span id="swrBlockedBrowsersCount" class="swrErrorMsg">%d</span> browsers. For more info - click <a href="#" class="swrBlacklistAddByBrowserBtn">Add by Browser</a> button', SWR_LANG_CODE)?>
				</span>
			</i>
        </li>
    </ul>
	<div style="clear: both;"></div>
</section>
<section>
	<div id="containerWrapper">
		<div class="supsystic-item supsystic-panel">
			<hr />
			<ul class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected', SWR_LANG_CODE)?>">
					<button class="button" id="swrBlacklistRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', SWR_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Clear All')?>">
					<button class="button" id="swrBlacklistClearBtn" disabled data-toolbar-button>
						<?php _e('Clear', SWR_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', SWR_LANG_CODE)?>">
					<input id="swrBlacklistTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', SWR_LANG_CODE)?>">
				</li>
			</ul>
			<div id="swrBlacklistTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<?php echo htmlSwr::selectbox('search_types', array('options' => $this->typesForSelect, 'attrs' => 'id="swrBlacklistTypeSel" onchange="swrBlacklistTypeSelChange();" class="supsystic-no-customize"', 'value' => $this->typeSelected))?>
			<div style="clear: both;"></div>
			<hr />
			<table id="swrBlacklistTbl"></table>
			<div id="swrBlacklistTblNav"></div>
			<div id="swrBlacklistTblEmptyMsg" style="display: none;">
				<h3><?php _e('No data found', SWR_LANG_CODE)?></h3>
			</div>
		</div>
	</div>
</section>
<!-- Add by IP dialog-->
<div id="swrBlacklistAddByIpDlg" title="<?php _e('Add IPs to blacklist', SWR_LANG_CODE)?>">
	<form id="swrBlacklistAddByIpForm">
		<label>
			<?php _e('Enter one or more IPs, each new IP - from new line', SWR_LANG_CODE)?>:
			<textarea name="ips" style="float: left; width: 100%; height: 230px;"></textarea>
		</label>
		<?php echo htmlSwr::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSwr::hidden('action', array('value' => 'addGroup'))?>
	</form>
	<div id="swrBlacklistAddByIpMsg"></div>
</div>
<!-- Add by Country dialog-->
<div id="swrBlacklistAddByCountryDlg" title="<?php _e('Add Country(es) to blacklist', SWR_LANG_CODE)?>">
	<form id="swrBlacklistAddByCountryForm">
		<?php _e('Select country(es) for blacklist')?>:<br />
		<?php echo htmlSwr::selectlist('country_ids[]', array('attrs' => 'class="chosen"', 'options' => $this->countryList, 'value' => $this->blockedCounties))?>
		<?php /*?><table width="100%" class="swrSmallTbl">
		<?php
			$perLine = 3;
			$i = 0;
		?>
		<?php foreach($this->countryList as $country) { ?>
			<?php if(!$i || $i % $perLine == 0) { ?>
				<tr>
			<?php }?>
			<td>
				<label>
					<?php
						$htmlParams = array('value' => $country['id']);
						if(in_array($country['id'], $this->blockedCounties)) {
							$htmlParams['checked'] = true;
						}
					?>
					<?php echo htmlSwr::checkbox('country_ids[]', $htmlParams)?>
					<?php echo $country['name']?>
				</label>
			</td>
			<?php if($i && $i % $perLine == $perLine - 1) { ?>
				</tr>
			<?php }?>
		<?php $i++; }?>
		</table><?php */?>
		<?php echo htmlSwr::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSwr::hidden('action', array('value' => 'addGroupCountries'))?>
	</form>
	<div id="swrBlacklistAddCountryMsg"></div>
</div>
<!-- Add by Browser dialog-->
<div id="swrBlacklistAddByBrowserDlg" title="<?php _e('Add Browser(s) to blacklist', SWR_LANG_CODE)?>">
	<form id="swrBlacklistAddByBrowserForm">
		<table width="100%" class="swrSmallTbl">
		<?php
			$perLine = 3;
			$i = 0;
		?>
		<?php foreach($this->browsersList as $browserName) { ?>
			<?php if(!$i || $i % $perLine == 0) { ?>
				<tr>
			<?php }?>
			<td>
				<label>
					<?php
						$htmlParams = array('value' => $browserName);
						if(in_array($browserName, $this->blockedBrowsers)) {
							$htmlParams['checked'] = true;
						}
					?>
					<?php echo htmlSwr::checkbox('browser_names[]', $htmlParams)?>
					<?php echo $browserName?>
				</label>
			</td>
			<?php if($i && $i % $perLine == $perLine - 1) { ?>
				</tr>
			<?php }?>
		<?php $i++; }?>
		</table>
		<?php echo htmlSwr::hidden('mod', array('value' => 'blacklist'))?>
		<?php echo htmlSwr::hidden('action', array('value' => 'addGroupBrowsers'))?>
	</form>
	<div id="swrBlacklistAddBrowserMsg"></div>
</div>