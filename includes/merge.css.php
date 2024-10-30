<?php
/**
* Class for merging CSS from all uploaded fonts
*
* @package   Cornerstone Custom icons
* @author    Michael Bourne
* @license   GPL3
* @link      https://ursa6.com
* @since     0.0.1
*/

if( ! defined( 'ABSPATH' ) ) {
    return;
}

class MergeCss_CCIcons extends CCIcons {

	public function __construct() {
		$this->generate_css();
	}

	/**
	 * Generate new CSS
	 */
	private function generate_css() {

		$options = get_option( 'cc_icons_fonts' );


		$css_content = "";
		if ( !empty( $options ) && is_array($options) ) {
			foreach ( $options as $key => $font ) {

				if ( isset( $font['status'] ) && $font['status'] !== '1' ){
					continue;
				}

				if ( empty( $font['data'] ) ) {
					continue;
				}


				$font_data = json_decode($font['data'],true);

				if ( isset($font_data['nameempty']) && $font_data['nameempty'] == true ){
					$fontfilename = 'fontello';
				} else {
					$fontfilename = strtolower( $font_data['name'] );
				}

				$style_parent_theme = wp_get_theme(get_template());
				$style_parent_theme_ver = $style_parent_theme->get( 'Version' );
				if (version_compare($style_parent_theme_ver, '2.2.0') >= 0) {
    				$weight = '900';
				} else {
					$weight = 'normal';
				}

				$randomver = mt_rand();
				$css_content .= "@font-face {
						 font-family: 'FontAwesome';
						  src: url('" . $font_data['font_url'] . "/" . $fontfilename  . ".eot?" . $randomver . "');
						  src: url('" . $font_data['font_url'] . "/" . $fontfilename  . ".eot?" . $randomver . "#iefix') format('embedded-opentype'),
						       url('" . $font_data['font_url'] . "/" . $fontfilename  . ".woff2?" . $randomver . "') format('woff2'),
						       url('" . $font_data['font_url'] . "/" . $fontfilename  . ".woff?" . $randomver . "') format('woff'),
						       url('" . $font_data['font_url'] . "/" . $fontfilename  . ".ttf?" . $randomver . "') format('truetype'),
						       url('" . $font_data['font_url'] . "/" . $fontfilename  . ".svg?" . $randomver . "#" . $fontfilename  . "') format('svg');
						  font-weight: " . $weight . ";
						  font-style: normal;
						}\n";


				$icons = cc_icons_manager()->parse_css( $font_data['css_root'], $font_data['name'] );

				// extra class added for the icons to appear in cornerstone builder.
				if (!empty($icons) && is_array($icons)){

					foreach ( $icons as $name_icon => $code ) {
						$css_content .= ".x-icon-" . $name_icon . ":before { content: '\\" . $code . "'; }\n";
						$css_content .= ".cs-fa-" . $name_icon . ":before { content: '\\" . $code . "'; }\n";
						$css_content .= "[data-cs-icon-id='" . $name_icon . "'] { font-family: 'FontAwesome'; }\n";
						$css_content .= "[data-cs-icon-id='" . $name_icon . "'] svg { display: none; }\n";
						//$css_content .= ".cs-icons-inner li[title='" . $name_icon . "']::before{ content: '\\" . $code . "'; }\n";
						//$css_content .= "div[data-cs-icon-id='" . $name_icon . "']::before{ content: '\\" . $code . "'; margin: calc((32px / 2) - .5em); font-size: 18px;}\n";
					}
				}

				$css_content .= "\n\n";

			}
		}

		$css_content = preg_replace( '/\t+/', '', $css_content );
		if ( is_dir( cc_icons_manager()->upload_dir  ) ) {
			file_put_contents( cc_icons_manager()->upload_dir . '/merged-icons-font.css', $css_content );
		}
	}

}