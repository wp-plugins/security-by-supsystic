<style type="text/css">
	.swrAdminMainLeftSide {
		width: 56%;
		float: left;
	}
	.swrAdminMainRightSide {
		width: <?php echo (empty($this->optsDisplayOnMainPage) ? 100 : 40)?>%;
		float: left;
		text-align: center;
	}
	#swrMainOccupancy {
		box-shadow: none !important;
	}
</style>
<section>
	<div class="supsystic-item supsystic-panel">
		<div>
			<p>
				<?php _e('Welcome to Supsystic Secure plugin - compleate way yo protect and secure your website.', SWR_LANG_CODE)?>
			</p>
			<p>
				<?php _e('You can check options and test one-by-one or start full test now.', SWR_LANG_CODE)?>
			</p>
		</div>
		<div id="containerWrapper">
			<?php if(!empty($this->optsDisplayOnMainPage)) { ?>
				<div class="swrAdminMainLeftSide">
				<?php foreach($this->optsDisplayOnMainPage as $cKey => $cData) { ?>
					<h2><?php echo $cData['label']?></h2>
					<?php foreach($cData['opts'] as $oKey => $oData) { ?>
						<?php
							$alertClass = 'info';
							if($oData['weight'] >= $this->notifyLevels['danger'])
								$alertClass = 'danger';
							elseif($oData['weight'] >= $this->notifyLevels['warning'])
								$alertClass = 'warning';
						?>
						<div class="alert alert-<?php echo $alertClass?>" style="margin-bottom: 5px; background-color: #f6f7f7;">
							<div style="float: left; padding-top: 7px;">
								<i class="fa fa-question supsystic-tooltip" title="<?php echo $oData['desc']?>"></i>
							</div>
							<div style="float: left; padding: 7px 0 0 20px;"><?php echo $oData['label']?></div>
							<div style="float: right;">
								<a href="<?php echo $cData['tab_url']?>" class="button button-primary">
									<?php if($cKey == frameSwr::_()->getModule('secure_files')->getCode()) { // I can just set "secure_files" here, but isn't this is cool?'?>
										<?php _e('Scan Now', SWR_LANG_CODE)?>
										<i class="fa fa-fw fa-level-up"></i>
									<?php } else { ?>
										<?php _e('Fix it', SWR_LANG_CODE)?>
									<?php }?>
								</a>
							</div>
							<div style="clear: both;"></div>
						</div>
					<?php }?>
				<?php }?>
				</div>
			<?php }?>
			<div class="swrAdminMainRightSide">
				<?php
					$scoreColor = '#54b1ff';
					if(100 - $this->occupancy['main'] >= $this->notifyLevels['danger'])
						$scoreColor = '#fa4e7f';
					elseif(100 - $this->occupancy['main'] >= $this->notifyLevels['warning'])
						$scoreColor = '#e89b07';
				?>
				<h2><?php _e('Security score', SWR_LANG_CODE)?></h2>
				<input type="text" value="<?php echo $this->occupancy['main']?>" id="swrMainOccupancy" data-fgColor="<?php echo $scoreColor?>" disabled />
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>