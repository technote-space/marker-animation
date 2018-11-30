<?php
/**
 * Technote Views Admin Test
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Controllers\Admin\Base $instance */
/** @var array $args */
/** @var \PHPUnit_Framework_TestResult $result */
/** @var \Technote\Tests\Base $class */
/** @var array $dump */
$instance->add_style_view( 'admin/style/table' );
?>

<h2><?php $instance->h( $class->class_name ); ?></h2>
<table class="widefat striped">
    <tr>
        <td><?php $instance->h( 'Count', true ); ?></td>
        <td><?php $instance->h( $result->count() ); ?></td>
    </tr>
    <tr>
        <td><?php $instance->h( 'Elapsed Time', true ); ?></td>
        <td><?php $instance->h( round( $result->time(), 6 ) ); ?> sec</td>
    </tr>
    <tr>
        <td><?php $instance->h( 'Passed', true ); ?></td>
        <td>
            <ul>
				<?php foreach ( $result->passed() as $test => $item ): ?>
                    <li><?php $instance->h( $test ); ?></li>
				<?php endforeach; ?>
            </ul>
        </td>
    </tr>
	<?php if ( method_exists( $result, 'warningCount' ) && method_exists( $result, 'warnings' ) ): ?>
        <tr>
            <td><?php $instance->h( 'Warning', true ); ?></td>
            <td>
				<?php if ( $result->warningCount() > 0 ): ?>
                    <ul>
						<?php foreach ( $result->warnings() as $item ): ?>
							<?php /** @var \PHPUnit_Framework_TestFailure $item */ ?>
                            <li><?php $instance->h( $item->toString() ); ?></li>
						<?php endforeach; ?>
                    </ul>
				<?php endif; ?>
            </td>
        </tr>
	<?php endif; ?>
	<?php if ( method_exists( $result, 'errorCount' ) && method_exists( $result, 'errors' ) ): ?>
        <tr>
            <td><?php $instance->h( 'Errors', true ); ?></td>
            <td>
				<?php if ( $result->errorCount() > 0 ): ?>
                    <ul>
						<?php foreach ( $result->errors() as $item ): ?>
							<?php /** @var \PHPUnit_Framework_TestFailure $item */ ?>
                            <li>
								<?php $instance->h( $item->toString() ); ?>
								<?php $instance->dump( $item->thrownException()->getTraceAsString() ); ?>
                            </li>
						<?php endforeach; ?>
                    </ul>
				<?php endif; ?>
            </td>
        </tr>
	<?php endif; ?>
	<?php if ( method_exists( $result, 'failureCount' ) && method_exists( $result, 'failures' ) ): ?>
        <tr>
            <td><?php $instance->h( 'Failure', true ); ?></td>
            <td>
				<?php if ( $result->failureCount() > 0 ): ?>
                    <ul>
						<?php foreach ( $result->failures() as $item ): ?>
							<?php /** @var \PHPUnit_Framework_TestFailure $item */ ?>
                            <li><?php $instance->h( $item->toString() ); ?></li>
						<?php endforeach; ?>
                    </ul>
				<?php endif; ?>
            </td>
        </tr>
	<?php endif; ?>
	<?php if ( method_exists( $result, 'riskyCount' ) && method_exists( $result, 'risky' ) ): ?>
        <tr>
            <td><?php $instance->h( 'Risky', true ); ?></td>
            <td>
				<?php if ( $result->riskyCount() > 0 ): ?>
                    <ul>
						<?php foreach ( $result->risky() as $item ): ?>
							<?php /** @var \PHPUnit_Framework_TestFailure $item */ ?>
                            <li><?php $instance->h( $item->toString() ); ?></li>
						<?php endforeach; ?>
                    </ul>
				<?php endif; ?>
            </td>
        </tr>
	<?php endif; ?>
	<?php if ( method_exists( $result, 'notImplementedCount' ) && method_exists( $result, 'notImplemented' ) ): ?>
        <tr>
            <td><?php $instance->h( 'Not Implemented', true ); ?></td>
            <td>
				<?php if ( $result->notImplementedCount() > 0 ): ?>
                    <ul>
						<?php foreach ( $result->notImplemented() as $item ): ?>
							<?php /** @var \PHPUnit_Framework_TestFailure $item */ ?>
                            <li><?php $instance->h( $item->toString() ); ?></li>
						<?php endforeach; ?>
                    </ul>
				<?php endif; ?>
            </td>
        </tr>
	<?php endif; ?>
	<?php if ( ! empty( $dump ) ): ?>
        <tr>
            <td><?php $instance->h( 'Dump', true ); ?></td>
            <td>
                <ul>
					<?php foreach ( $dump as $item ): ?>
                        <li>
                            <pre><?php $instance->h( $item ); ?></pre>
                        </li>
					<?php endforeach; ?>
                </ul>
            </td>
        </tr>
	<?php endif; ?>
</table>

