<?php 
	$elementor_template = get_theme_mod('footer_elementor_template_setting', 'default'); 
	$copyrights_text = get_theme_mod('copyright_text_setting', __('&copy;2025 All rights reserved. Powered by Brainforward', 'brainforward'));
?>
			</div>
		</main>
		<?php if (class_exists( 'Elementor\Plugin' ) && !empty($elementor_template) && $elementor_template !== 'default'): ?>
			<div class="footer__elementor__section">
				<?php echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($elementor_template); ?>
			</div>
		<?php else: ?>
		<footer class="footer_wrapper">
			<?php if (!empty($copyrights_text)): ?>
				<div class="container">
					<div class="text-center py-4">
						<?php echo wp_kses_post($copyrights_text); ?>
					</div>
				</div>
			<?php endif; ?>
		</footer>
		<?php endif; ?>
	<?php wp_footer(); ?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K22XJJNQ"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	</body>
</html>
