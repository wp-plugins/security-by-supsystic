<div class="wrap">
    <div class="supsystic-plugin">
		<?php echo $this->breadcrumbs?>
        <section class="supsystic-content">
            <nav class="supsystic-navigation supsystic-sticky">
                <ul>
					<?php foreach($this->tabs as $tabKey => $tab) { ?>
						<li class="<?php echo ($this->activeTab == $tabKey ? 'active' : '')?>">
							<a href="<?php echo $tab['url']?>">
								<?php if(isset($tab['fa_icon'])) { ?>
									<i class="fa <?php echo $tab['fa_icon']?>"></i>	
								<?php } elseif(isset($tab['wp_icon'])) { ?>
									<i class="dashicons-before <?php echo $tab['wp_icon']?>"></i>	
								<?php } elseif(isset($tab['icon'])) { ?>
									<i class="<?php echo $tab['icon']?>"></i>	
								<?php }?>
								<?php echo $tab['label']?>
							</a>
						</li>
					<?php }?>
                </ul>
            </nav>
            <div class="supsystic-container">
				<?php echo $this->content?>
                <div class="clear"></div>
            </div>
        </section>
    </div>
</div>
