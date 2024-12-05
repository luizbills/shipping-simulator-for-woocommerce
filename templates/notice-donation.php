<?php
/**
 * The Template for displaying an admin notice asking for reviews or donations.
 *
 * @var string $class The wrapper CSS classes
 * @var bool $is_dismissible This notice is not dismissible in settings page
 * @var string $cookie The cookie nama
 * @var int $cookie_max_age The cookie duration
 */

use Shipping_Simulator\Helpers as h;

$five_stars = '&#9733;&#9733;&#9733;&#9733;&#9733;';
$message = sprintf(
    /* translators: %1$s is replaced with plugin name and %2$s with 5 stars */
    esc_html__( 'Help us keep the %1$s plugin free and always up to date making a donation or rating %2$s on WordPress.org.', 'wc-shipping-simulator' ),
    '<strong>' . esc_html( h::config_get( 'NAME' ) ) . '</strong>',
    $five_stars
);
?>

<div class="<?php echo esc_attr( $class ) ?>" id="wc_shipping_sim_notice_donation">
    <p><?php echo $message; ?></p>
    <p>
        <a href="<?php echo esc_url( h::config_get( 'DONATION_URL' ) ) ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Donate', 'wc-shipping-simulator' ); ?></a>
        <a href="<?php echo esc_attr( h::config_get( 'PLUGIN_REVIEWS' ) ) ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'Make a review', 'wc-shipping-simulator' ); ?></a>
    </p>
    <?php if ( $is_dismissible ) : ?>
    <button type="button" class="notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice.' ); ?>">
        <span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.' ); ?></span>
    </button>
    <?php endif; ?>
</div>

<?php if ( $is_dismissible ) : ?>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const notice = document.querySelector('#wc_shipping_sim_notice_donation .notice-dismiss');
        notice.addEventListener('click', (evt) => {
            const cookie = '<?php echo esc_js( "{$cookie}=yes;max-age={$cookie_max_age};secure;samesite=strict" ) ?>';
            document.cookie = cookie;
            evt.currentTarget.parentNode.style.display = 'none';
            evt.currentTarget.parentNode.style.visibility = 'hidden';
        })
    })
</script>
<?php endif ?>
