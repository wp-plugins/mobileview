<?php global $wpmobi; ?>
<?php $settings = $wpmobi->get_settings(); ?>
<script type="text/javascript"><!--
google_ad_client = "<?php echo $settings->adsense_id; ?>";
/* Mobile */
google_ad_slot = "<?php echo $settings->adsense_slot_id; ?>";
google_ad_width = 320;
google_ad_height = 50;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
