<?php
$dashboard_permalink = apply_filters( 'futurewordpress/project/system/getoption', 'permalink-dashboard', 'dashboard' );
$dashboard_permalink = site_url( $dashboard_permalink );
?>
<?php do_action( 'futurewordpress/project/parts/call', 'before_homecontent' ); ?>
<div>
    <?php do_action( 'futurewordpress/project/parts/call', 'homecontent' ); ?>
</div>
<?php do_action( 'futurewordpress/project/parts/call', 'after_homecontent' ); ?>
