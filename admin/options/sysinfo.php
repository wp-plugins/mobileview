<div id="system-info">
	<table>
		<tr>
			<td class="desc"><?php _e( "WordPress Version", "mobileviewlang" ); ?></td>
			<td><?php echo sprintf( __( "%s", "mobileviewlang" ), get_bloginfo( 'version' ) ); ?></td>
		</tr>			
		<tr>
			<td class="desc"><?php _e( "Server Configuration", "mobileviewlang" ); ?></td>
			<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?>, <?php echo $_SERVER['GATEWAY_INTERFACE']; ?>, PHP <?php echo phpversion(); ?>, <?php $con = mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD ); if ( mysqli_connect_errno() ) { die( 'Could not connect: ' . mysqli_connect_error() ); } printf( "MySQL %s", mysqli_get_server_info($con) ) ;mysqli_close($con);?></td>
		</tr>
		<tr>
			<td class="desc"><?php _e( "Browser User Agent", "mobileviewlang" ); ?></td>
			<td><?php echo $_SERVER['HTTP_USER_AGENT']; ?></td>
		<tr/>
	</table>
</div>