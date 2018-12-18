<?php
/**
 * Technote Views Admin Include Pagination
 *
 * @version 2.7.0
 * @author technote-space
 * @since 2.7.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
/** @var int $page */
/** @var int $total_page */
/** @var string $p */
$instance->add_style_view( 'admin/style/pagination' );
?>
<div class="pagination">
	<?php if ( $page > 1 ) : ?>
		<?php $instance->url( $instance->app->input->get_current_url( [ $p => $page - 1 ] ), '<', true, false, [
			'class' => 'pagination-item',
		] ); ?>
	<?php else: ?>
        <div class="pagination-item">
			<?php $instance->h( '<', true ); ?>
        </div>
	<?php endif; ?>
    <div class="pagination-item pagination-now">
		<?php $instance->h( $page ); ?>
    </div>
	<?php if ( $page < $total_page ): ?>
		<?php $instance->url( $instance->app->input->get_current_url( [ $p => $page + 1 ] ), '>', true, false, [
			'class' => 'pagination-item',
		] ); ?>
	<?php else: ?>
        <div class="pagination-item">
			<?php $instance->h( '>', true ); ?>
        </div>
	<?php endif; ?>
</div>