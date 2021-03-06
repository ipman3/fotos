<?php
/*
Section: Navigation
Author: Nick Haskins
Author URI: http://nickhaskins.co
Version: 1.0
Description: A full-featured navigation system with horizontal, vertical, fixed top, fixed bottom, and unlimited sub-menus.
Class Name: fotosNav
Filter: nav
*/

class fotosNav extends PageLinesSection {

	const version = '1.0';

	function section_persistent(){

        add_filter( 'pless_vars', array($this,'fotos_nav_less_vars'));
	}

	function fotos_nav_less_vars($less){

		$less['fotos-nav-height'] 	= pl_setting('ba_fotos_nav_height')  ? pl_setting('ba_fotos_nav_height') : '64px';
		$less['fotos-nav-base-color'] = pl_setting('ba_fotos_nav_base_color')  ? pl_hashify(pl_setting('ba_fotos_nav_base_color')) : '#333';
		$less['fotos-nav-font-color'] = pl_setting('ba_fotos_nav_font_color')  ? pl_hashify(pl_setting('ba_fotos_nav_font_color')) : '#f8f8f8';
		$less['fotos-nav-font-size'] 	= pl_setting('ba_fotos_nav_font_size' )  ? pl_setting('ba_fotos_nav_font_size') : '14px';

		return $less;
	}

	function section_scripts() {
		wp_enqueue_script('fotos-nav',$this->base_url.'/jquery.yams.min.js',array('jquery'),self::version,true);
	}

	function section_head() {

		$fixpos = $this->opt('ba_fotos_nav_nav_mode');
		$dropup = ('fotos-nav-fixed-bott' == $fixpos) ? true : 0;
		$id = $this->get_the_id();
		$margin = pl_setting('ba_fotos_nav_margin');

		?><script>
			jQuery(window).load(function(){

				jQuery('.fotos-nav-menu.fotos-nav-menu-<?php echo $this->get_the_id();?>').smartmenus({
					bottomToTopSubMenus:<?php echo $dropup;?>
				});

				<?php if('fotos-nav-fixed-top' == $fixpos) { ?>
					adminbar = jQuery('#wpadminbar').height();
					jQuery('#fotos-navigation<?php echo $id;?>').css({'top':adminbar});
				<?php } elseif('fotos-nav-fixed-bott' == $fixpos) { ?>
					toolbar = jQuery('#PageLinesToolbox').height();
					jQuery('#fotos-navigation<?php echo $id;?>').css({'bottom':toolbar});
				<?php } else { ?>
					return false;
				<?php  } ?>
			});
		</script><?php
		
		if($fixpos) {

			$top = ('fotos-nav-fixed-top' == $fixpos) ? 'fotos-nav-fixed fotos-nav-fixed-top' : false;
			$top .= ('fotos-nav-fixed-bott' == $fixpos) ? 'fotos-nav-fixed fotos-nav-fixed-bottom' : false;
			$top .= ('fotos-nav-vert-mode' == $fixpos) ? 'fotos-nav-vert-mode' : false;

			pagelines_add_bodyclass($top);
		}

		if($margin) { ?>
			<style>
			.fotos-nav-menu {
				margin:<?php echo $margin;?>
			}
			</style>
		<?php }

	}


 	function section_template() {

 		$menu 	= $this->opt( 'ba_fotos_nav_menu' );
 		$fixpos = $this->opt('ba_fotos_nav_nav_mode');
 		$mode 	= ('fotos-nav-vert-mode' == $fixpos) ? 'sm-vertical' : false;
 		$id 	= $this->get_the_id();
 		$align  = $this->opt('ba_fotos_nav_nav_align') ? $this->opt('ba_fotos_nav_nav_align') : 'fotos-nav-left';
 		$navbg 	= pl_setting('ba_fotos_nav_bg_img') ? sprintf('style="background-image:url(\'%s\')"',pl_setting('ba_fotos_nav_bg_img')) : false;
 		
 		$getmin	= pl_setting('ba_fotos_nav_minimal');
 		$mini	= $getmin ? 'fotos-nav-minimal' : 'fotos-nav-hascolor';

		echo '<nav itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement" class="fotos-nav fix '.$align.' '.$mini.'" role="navigation" '.$navbg.'>';

			if($this->opt('ba_fotos_nav_do_search'))
				get_search_form();

			$args = array(
				'menu_class'  	=> 'sm '.$mode.' fotos-nav-menu fotos-nav-menu-'.$id,
				'menu'			=> $menu,
				'depth' 		=> 10,
				'fallback_cb'   => 'pl_nav_callback'
			);
			wp_nav_menu( $args );

		echo '</nav>';

	}


	// Global Message
	function welcome_global(){

        ob_start();
        ?>
            <div style="color:#444;">
                <p style="border-bottom:1px solid #ccc;margin:0 0 0.75em;"><strong><?php _e('Instructions','fotos');?></strong></p>
                <ul class="unstyled" style="font-size:12px;line-height:14px;">
                    <li style="margin-bottom:7px;"><?php _e('These are the global options for Basiq Nav. Due to the way PageLines sections are handled in DMS, these options have to be global instead of in the section. Options for the individual section can be found by editing the actual section itself on the page.','fotos');?></li>
                </ul>
            </div>
        <?php
        return ob_get_clean();
	}


	function section_opts( ){

		$options = array();

		$options[] = array(
			'title'   					=> __('Choose a Menu', 'fotos'),
		    'type'    					=> 'select_menu',
		    'key' 						=> 'ba_fotos_nav_menu',
			'help' 						=> __('Select a menu to use.' , 'fotos'),
		);

		$options[] = array(
			'title'   					=> __('Nav Alignment', 'fotos'),
		    'type'    					=> 'select',
		    'key'						=> 'ba_fotos_nav_nav_align',
		    'default'					=> 'fotos-nav-left',
		    'opts'						=> array(
		    	'fotos-nav-left' 		=> array('name' => __('Align Left','fotos')),
		    	'fotos-nav-right' 		=> array('name' => __('Align Right','fotos')),
		    	'fotos-nav-center' 		=> array('name' => __('Centered','fotos'))
		    )
		);

		$options[] = array(
			'title'   					=> __('Nav Modes', 'fotos'),
		    'type'    					=> 'select',
		    'key'						=> 'ba_fotos_nav_nav_mode',
		    'default'					=> 'standard',
		    'opts'						=> array(
		    	'standard' 				=> array('name' => __('Standard','fotos')),
		    	'fotos-nav-fixed-top' 	=> array('name' => __('Fixed Top','fotos')),
		    	'fotos-nav-vert-mode' 	=> array('name' => __('Vertical Mode','fotos'))
		    ),
			'help' 						=> __('Choose a fixed position for the nav. If "Fixed Bottom" is chosen, sub-menus will drop-up, instead of down. Vertical Mode will run the menu as a vertical menu.' , 'fotos'),
		);

		$options[] = array(
			'title'   					=> __('Enable Search', 'fotos'),
		    'type'    					=> 'check',
		    'key' 						=> 'ba_fotos_nav_do_search',
			'help' 						=> __('Enable a search form that fits inside the navigation.' , 'fotos'),
		);

		return $options;

	}

}