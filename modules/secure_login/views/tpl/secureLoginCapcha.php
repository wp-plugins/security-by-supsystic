<script type="text/javascript">
var RecaptchaOptions = {
	theme : 'custom'
,	custom_theme_widget: 'recaptcha_widget'
};
</script>
<style type="text/css">
	.recaptcha_widget{
		-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;max-width:300px;border:4px solid #AF1500;
		-webkit-border-radius:4px;-moz-border-radius:4px;-ms-border-radius:4px;-o-border-radius:4px;border-radius:4px;background:#AF1500;
		margin:0 0 10px}
	#recaptcha_image{
		width:100% !important;height:auto !important}
	#recaptcha_image img{
		-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;width:100%;
		height:auto;-webkit-border-radius:2px;-moz-border-radius:2px;-ms-border-radius:2px;-o-border-radius:2px;
		border-radius:2px;
		/*border:3px solid #FFF*/}
	.recaptcha_is_showing_audio embed{
		height:0;width:0;overflow:hidden}
	.recaptcha_is_showing_audio #recaptcha_image{
		-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;width:100%;
		height:60px;background:#FFF;-webkit-border-radius:2px;-moz-border-radius:2px;-ms-border-radius:2px;-o-border-radius:2px;border-radius:2px;
		border:3px solid #FFF}
	.recaptcha_is_showing_audio #recaptcha_image br{
		display:none}
	.recaptcha_is_showing_audio #recaptcha_image #recaptcha_audio_download{
		display:block}
	.recaptcha_input{
		background:#FFDC73;color:#000;font:13px/1.5 "HelveticaNeue","Helvetica Neue",Helvetica,Arial,"Liberation Sans",FreeSans,sans-serif;
		margin:4px 0 0;padding:0 4px 4px;border:4px solid #FFDC73;
		-webkit-border-radius:2px;-moz-border-radius:2px;-ms-border-radius:2px;-o-border-radius:2px;border-radius:2px}
	.recaptcha_input label{margin:0 0 6px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
	.recaptcha_input input{width:100%}.recaptcha_options{list-style:none;margin:4px 0 0;height:18px}
	.recaptcha_options li{float:left;margin:0 4px 0 0}
	.recaptcha_options li a{text-decoration:none;text-shadow:0 1px 1px #000;font-size:16px;color:#FFF;display:block;width:20px;height:18px}
	.recaptcha_options li a:active{position:relative;top:1px;text-shadow:none}
	.captcha_hide{
		display:none}
</style>
<div id="recaptcha_widget" style="display:none" class="recaptcha_widget">
	<div id="recaptcha_image"></div>
	<div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect. Please try again.</div>

	<div class="recaptcha_input">
		<label class="recaptcha_only_if_image" for="recaptcha_response_field">Enter the words above:</label>
		<label class="recaptcha_only_if_audio" for="recaptcha_response_field">Enter the numbers you hear:</label>

		<input type="text" id="recaptcha_response_field" name="recaptcha_response_field">
	</div>
	<ul class="recaptcha_options">
		<li>
			<a href="javascript:Recaptcha.reload()">
				<i class="fa fa-refresh"></i>
				<span class="captcha_hide">Get another CAPTCHA</span>
			</a>
		</li>
		<li class="recaptcha_only_if_image">
			<a href="javascript:Recaptcha.switch_type('audio')">
				<i class="fa fa-volume-up"></i><span class="captcha_hide"> Get an audio CAPTCHA</span>
			</a>
		</li>
		<li class="recaptcha_only_if_audio">
			<a href="javascript:Recaptcha.switch_type('image')">
				<i class="fa fa-picture-o"></i><span class="captcha_hide"> Get an image CAPTCHA</span>
			</a>
		</li>
		<li>
			<a href="javascript:Recaptcha.showhelp()">
				<i class="fa fa-question"></i><span class="captcha_hide"> Help</span>
			</a>
		</li>
	</ul>
</div>

<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $this->publicKey?>"></script>
<noscript>
	<iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $this->publicKey?>" height="300" width="500" frameborder="0"></iframe><br>
	<textarea name="recaptcha_challenge_field"></textarea>
	<input type="hidden" name="recaptcha_response_field" value="manual_challenge">
</noscript>


