<?php do_action("bii_options_submit"); ?>
<div class="bii_options_page">
	<div class="message"><?php
		if (isset($_SESSION["bii_message"])) {
			echo $_SESSION["bii_message"];
		}
		?></div>
	<div class="titre ">
		<?= apply_filters('bii_options_page_title', 0, 0); ?>
	</div>
	<div class="col-xxs-12 col-md-10">
		<div class="col-xxs-12">
			<div class="meta-box-holder">
				<ul class="nav nav-tabs bii-option-title">
					<?php do_action("bii_options_title"); ?> 
				</ul>
				<form method="post" id="poststuff" action="<?= apply_filters("bii_options_page_link", null) ?>">
					<?php do_action("bii_options"); ?> 
					<?php if (bii_canshow_debug()) { 
						ini_set('display_errors', '1');
						?>
						<div class="col-xxs-12 pl-zdt bii_option hidden">
							<h2 class="faa-parent animated-hover"><i class="fa fa-cogs faa-ring"></i> Zone de test</h2>
							<?php
							do_action("bii_plugin_test_zone");
							?>
						</div>
					<?php } ?>
					<div class="clear"></div>
					<button class="publier btn btn-success hidden" accesskey="p" tabindex="5"><span class="fa fa-save"></span> Enregistrer les modifications</button>
				</form>
			</div>
		</div>
	</div>
</div>