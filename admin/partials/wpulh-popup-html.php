<?php 
if (is_user_logged_in()) 
{ 
	?>
	<div id="wpulh-model-html">
		<!-- Login section --> 
		<div id="wp-users-login-history-container" class="wpulh-modal"> 
	 		<div  class="wpulh-modal-content animate">
	 			<div  class="wpulh-modal-heading">
					<h2 class="wpulh-username"></h2>
					<p class="">
						<button type="button" class="button wpulh-container-close-btn" name="wpulh-close-btn" value="<?php esc_attr_e( 'Close', 'wpulh' ); ?>"><?php esc_html_e( 'Close', 'wpulh' ); ?></button>
					</p>
				</div>
				<form class="wpulh-login" method="post">
					<div class="wpulh-container"> 
						<div class="wpulh-history-container"> 
						</div>
						
					</div>
				</form>
			</div>
		</div>
		
<?php } ?>