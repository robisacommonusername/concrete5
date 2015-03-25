<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?
if (is_array($image)) {
	$image = $image[0];
}
?>
<div class="ccm-gathering-image-byline-description-center ccm-gathering-centered-content ccm-gathering-scaled-image">
	<a href="#" data-overlay="gathering-item">
		<img src="<?=$image->getSrc()?>" alt="<?php echo t('Preview Image') ?>" />
	</a>
	<div class="ccm-gathering-tile-title-description">
		<div class="ccm-gathering-tile-byline"><?php echo tc(/*i18n: %s is the name of the author */ 'Authored', 'By %s', '<span class="author-name">' . $author . '</span>'); ?></div>
		<div class="ccm-gathering-tile-description">
		<?=$description?>
		</div>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>
