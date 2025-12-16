<?php
/**
 * Display single login
 *
 * @package Tutor\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// যদি ইউজার আগে থেকেই লগইন করা থাকে, তাহলে dashboard লোড করো
if ( is_user_logged_in() && ! is_admin() ) {
	tutor_load_template( 'dashboard.logged-in' );
	return;
}

// Checkout ইনিশিয়ালাইজ
$checkout = WC()->checkout();
// 🔹 যদি গেস্ট চেকআউট **অনুমোদিত থাকে**, তাহলে return করো (ফর্ম দেখানোর দরকার নেই)
if ( !$checkout->is_registration_enabled() && ! $checkout->is_registration_required() ) {
	return;
}

add_filter(
	'tutor_after_login_redirect_url',
	function() {
		return tutor_utils()->tutor_dashboard_url();
	}
);

do_action( 'tutor/template/login/before/wrap' );
?>

<div class="bf-login-body">
	<div class="bf-login-header">
		<div class="bf-login-tabs">
			<button type="button" class="bf-tab-button email-button active" data-tab="bf-email-login">
				<?php esc_html_e( 'ইমেইল দিয়ে সাইনআপ', 'brainfwd' ); ?>
			</button>
			<button type="button" class="bf-tab-button mobile-button" data-tab="bf-mobile-login">
				<?php esc_html_e( 'নম্বর দিয়ে সাইনআপ', 'brainfwd' ); ?>
			</button>
		</div>
	</div>
	<div class="bf-login-content">
	    
		<!-- brainforward Email Login -->
		<div id="bf-email-login" class="bf-login-tab-content active">
			<?php
				$login_form = trailingslashit( tutor()->path ) . 'templates/login-form.php';
				tutor_load_template_from_custom_path( $login_form, false );
			?>
		</div>
		
		<!-- brainforward Mobile Login -->
		<div id="bf-mobile-login" class="bf-login-tab-content">
			<div class="bf-mobile-login-form">
				<!-- 🔹 Step 1: Number Check Section -->
				<form method="post" id="bf-number-section">
					<p class="bf-form-row">
						<label for="bf_mobile_number">
							<?php esc_html_e( 'মোবাইল নম্বর', 'brainfwd' ); ?>
						</label>
						<input
							type="text"
							name="bf_mobile_number"
							id="bf_mobile_number"
							class="mb-2"
							required
							placeholder="<?php esc_attr_e( 'উদাহরণ: ০১XXXXXXXXX', 'brainfwd' ); ?>">
					</p>
					<button type="submit" id="bf-send-otp" class="button button-primary">
						<?php esc_html_e( 'সাবমিট করুন', 'brainfwd' ); ?>
					</button>
				</form>

				<!-- 🔹 Step 2: Password Section -->
				<form method="post" id="bf-password-section" style="display:none;">
					<p class="bf-form-row">
						<label for="bf_mobile_password">
							<?php esc_html_e( 'পাসওয়ার্ড', 'brainfwd' ); ?>
						</label>
						<span class="position-relative d-block">
							<input
								type="password"
								name="bf_mobile_password"
								id="bf_mobile_password"
								class="mb-2"
								required
								placeholder="<?php esc_attr_e( 'আপনার পাসওয়ার্ড লিখুন', 'brainfwd' ); ?>">
							<i id="bf-password-toggle" class="ri-eye-line"></i>
						</span>
					</p>
					<button type="button" id="bf-forgot-button" class="button-link">
						<?php esc_html_e( 'পাসওয়ার্ড ভুলে গেছেন?', 'brainfwd' ); ?>
					</button>
					<button type="submit" id="bf-password-button" class="button button-primary">
						<?php esc_html_e( 'লগইন করুন', 'brainfwd' ); ?>
					</button>
				</form>

				<!-- 🔹 Step 3: OTP Section -->
				<form method="post" id="bf-otp-section" style="display:none;">
					<p class="bf-form-row">
						<label for="bf_otp_code">
							<?php esc_html_e( 'OTP লিখুন', 'brainfwd' ); ?>
						</label>
						<input
							type="text"
							name="bf_otp_code"
							id="bf_otp_code"
							placeholder="<?php esc_attr_e( '৬-সংখ্যার OTP কোড', 'brainfwd' ); ?>">
					</p>
					<button type="submit" id="bf-verify-otp" class="button button-primary">
						<?php esc_html_e( 'ভেরিফাই করুন ও লগইন করুন', 'brainfwd' ); ?>
					</button>
				</form>

			</div>
		</div>
	</div>
</div>

<?php
do_action( 'tutor/template/login/after/wrap' );
?>
