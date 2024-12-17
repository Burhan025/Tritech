<?php
/*
Template Name: Blog
*/

remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
remove_action( 'genesis_before_entry_content', 'genesis_do_breadcrumbs' );
//add_action( 'loop_start', 'genesis_do_breadcrumbs' );

remove_action( 'genesis_loop', 'genesis_do_loop' );
remove_action( 'genesis_loop', 'genesis_standard_loop' );

//* Remove the entry title (requires HTML5 theme support)
//remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

// Custom Loop for Blog
add_action( 'genesis_loop', 'custom_blog' );
function custom_blog() { 

	$cbi = custom_background_image();

	printf( '<section class="subpage-header" style="background-image:url(' . $cbi . ');">' );

		printf( '<div class="wrap"><article><header class="entry-header">' );
			
		if( ( is_category() || is_tag() || is_tax() ) ) {
			$cat_title = single_cat_title('', false);
			echo '<h1 class="entry-title" itemprop="headline">' . $cat_title . '</h1>';
		}
		else {
			echo '<h1 class="entry-title" itemprop="headline">Blog</h1>';
		}

			//woocommerce_breadcrumb();

		printf( '</header></article></div>');

	printf( '</section>');

	printf( '<section class="subpage-container"><div class="wrap">' );

		echo '<div class="three-fourths first">';

		//* Use old loop hook structure if not supporting HTML5
		if ( ! genesis_html5() ) {
			genesis_legacy_loop();
			return;
		}

		if ( have_posts() ) :

			do_action( 'genesis_before_while' );
			while ( have_posts() ) : the_post();

				do_action( 'genesis_before_entry' );

				printf( '<article %s>', genesis_attr( 'entry' ) );

					do_action( 'genesis_entry_header' );

					do_action( 'genesis_before_entry_content' );

					printf( '<div %s>', genesis_attr( 'entry-content' ) );
					do_action( 'genesis_entry_content' );
					echo '</div>';

					do_action( 'genesis_after_entry_content' );

					do_action( 'genesis_entry_footer' );

				echo '</article>';

				do_action( 'genesis_after_entry' );

			endwhile; //* end of one post
			do_action( 'genesis_after_endwhile' );

		else : //* if no posts exist
			do_action( 'genesis_loop_else' );
		endif; //* end loop

		echo '</div>';

		echo '<div class="one-fourth sidebar sidebar-alt widget-area">'; 
			dynamic_sidebar('blog-sidebar');
		echo '</div>';

	echo '</div></section>';

}
//* Run the Genesis loop
genesis();
