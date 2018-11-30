<?php
/**
 * Technote Views Admin Include Layout
 *
 * @version 1.1.68
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
/** @var string $slug */
/** @var \Technote\Controllers\Admin\Base $page */
$instance->add_style_view( 'admin/style/button' );
?>
<div class="wrap <?php $instance->id(); ?>-wrap">
    <div class="icon32 icon32-<?php $instance->h( $slug ); ?>"><br/></div>
    <div id="<?php $instance->id(); ?>-main-contents">
        <h2 id="<?php $instance->id(); ?>-page_title"><?php $instance->h( $page->get_page_title(), true ); ?></h2>
		<?php echo $page->presenter(); ?>
    </div>
</div>