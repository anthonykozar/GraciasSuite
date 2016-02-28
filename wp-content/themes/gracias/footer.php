		<?php get_sidebar( 'footer-wide' ); ?>
		<div id="footer">
			<?php get_sidebar( 'footer' ); ?>
			<div id="copyright">
				<?php if( pinboard_get_option( 'theme_credit_link' ) || pinboard_get_option( 'author_credit_link' )  || pinboard_get_option( 'wordpress_credit_link' ) ) : ?>
					<p class="credits twocol"><a href="/site-credits/">Site Credits</a></p>
				<?php endif; ?>
				<p class="copyright twocol"><?php pinboard_copyright_notice(); ?></p>
				<div class="clear"></div>
			</div><!-- #copyright -->
		</div><!-- #footer -->
	</div><!-- #wrapper -->
<?php wp_footer(); ?>
</body>
</html>
