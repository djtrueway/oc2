<div class="es-powered">
	<p>
		<?php
		$url = 'http://estatik.net';

		$link = sprintf( wp_kses( __( 'Powered by <a href="%s" target="_blank">Estatik</a>', 'es-plugin' ), array(
			'a' => array( 'href' => array(), 'target' => array() ) )
		), esc_url( $url ) );

		echo $link; ?>
	</p>
</div>
