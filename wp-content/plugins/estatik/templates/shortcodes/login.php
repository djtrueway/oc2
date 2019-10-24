<?php global $es_settings; $messenger = new Es_Messenger( 'login' ); ?>

<?php if ( ! is_user_logged_in() ) : ?>
	<div class="es-login__wrap">
		<h2><?php _e( 'Log in', 'es-plugin' ); ?></h2>

		<?php $messenger->render_messages(); ?>

		<form action="" method="post">

			<div class="es-field__wrap es-field-icon">
				<label for="es-user-login">
					<i class="fa fa-user" aria-hidden="true"></i>
					<input id="es-user-login" name="log" type="text" placeholder="<?php _e( 'Username', 'es-plugin' ); ?>"/>
				</label>
			</div>

			<div class="es-field__wrap es-field-icon">
				<label for="es-user-pwd">
					<i class="fa fa-lock" aria-hidden="true"></i>
					<input type="password" name="pwd" id="es-user-pwd" placeholder="<?php _e( 'Password', 'es-plugin' ); ?>">
				</label>
			</div>

			<div class="es-submit__wrap">
				<input type="submit" class="es-btn es-btn-orange" value="<?php _e( 'Log in', 'es-plugin' ); ?>">
			</div>

			<div class="es-login__links">
				<?php if ( $es_settings->reset_password_page_id && ( get_post( $es_settings->reset_password_page_id ) ) ) : ?>
					<a href="<?php echo get_the_permalink( $es_settings->reset_password_page_id ); ?>"><?php _e( 'I forgot my password', 'es-plugin' ); ?></a><br/>
				<?php endif; ?>
				<?php if ( $es_settings->registration_page_id && ( get_post( $es_settings->registration_page_id ) ) ) : ?>
					<a href="<?php echo get_the_permalink( $es_settings->registration_page_id ); ?>">
						<?php _e( 'I need to register for a new account', 'es-plugin' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<input type="hidden" name="redirect" value="<?php the_permalink(); ?>" />

			<?php wp_nonce_field( 'es-login', 'es-login' ); ?>
		</form>
	</div>
<?php else: ?>
	<div class="es-agent-register__logged">
		<?php _e( 'You are already logged in.', 'es-plugin' ); ?><br>
		<a href="<?php echo wp_logout_url( get_the_permalink() ); ?>" class="es-agent__logout es-btn es-btn-orange-bordered"><?php _e( 'Logout', 'es-plugin' ); ?></a>
	</div>
<?php endif;
