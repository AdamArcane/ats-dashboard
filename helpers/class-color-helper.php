<?php
/**
 * Color helper.
 *
 * @package ATS_Dashboard
 */

namespace ATSDash\Helpers;

defined( 'ABSPATH' ) || die( "Can't access directly" );

/**
 * Class to setup color helper.
 */
class Color_Helper {

	/**
	 * Get color in rgb format from the provided color in hex format.
	 *
	 * @param string $hex_color Color in hex format.
	 * @return array Array containing r,g,b.
	 */
	public function hex_to_rgb( $hex_color ) {

		$hex_color = str_replace( '#', '', $hex_color );
		$hex_color = trim( $hex_color );

		if ( 3 !== strlen( $hex_color ) && 6 !== strlen( $hex_color ) ) {
			return array( 255, 255, 255 );
		}

		if ( 3 === strlen( $hex_color ) ) {
			$r = hexdec( substr( $hex_color, 0, 1 ) . substr( $hex_color, 0, 1 ) );
			$g = hexdec( substr( $hex_color, 1, 1 ) . substr( $hex_color, 1, 1 ) );
			$b = hexdec( substr( $hex_color, 2, 1 ) . substr( $hex_color, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex_color, 0, 2 ) );
			$g = hexdec( substr( $hex_color, 2, 2 ) );
			$b = hexdec( substr( $hex_color, 4, 2 ) );
		}

		return array( $r, $g, $b );

	}

	/**
	 * Get color in hsl format from the provided color in hex format.
	 *
	 * @param string $hex_color Color in hex format.
	 * @return array Array containing hue (0-360), saturation and lightness (0-1).
	 */
	public function hex_to_hsl( $hex_color ) {

		$rgb = $this->hex_to_rgb( $hex_color );

		$r = $rgb[0] / 255;
		$g = $rgb[1] / 255;
		$b = $rgb[2] / 255;

		$max   = max( $r, $g, $b );
		$min   = min( $r, $g, $b );
		$delta = $max - $min;

		$l = ( $max + $min ) / 2;
		$h = 0;
		$s = 0;

		if ( $delta > 0 ) {
			$s = $l > 0.5 ? $delta / ( 2 - $max - $min ) : $delta / ( $max + $min );

			if ( $max === $r ) {
				$h = fmod( ( $g - $b ) / $delta, 6 );
			} elseif ( $max === $g ) {
				$h = ( $b - $r ) / $delta + 2;
			} else {
				$h = ( $r - $g ) / $delta + 4;
			}

			$h *= 60;

			if ( $h < 0 ) {
				$h += 360;
			}
		}

		return array( $h, $s, $l );

	}

	/**
	 * Get color in hex format from the provided hue, saturation and lightness.
	 *
	 * @param float $h Hue, 0-360.
	 * @param float $s Saturation, 0-1.
	 * @param float $l Lightness, 0-1.
	 * @return string Color in hex format.
	 */
	public function hsl_to_hex( $h, $s, $l ) {

		$c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
		$x = $c * ( 1 - abs( fmod( $h / 60, 2 ) - 1 ) );
		$m = $l - $c / 2;

		if ( $h < 60 ) {
			$rgb = array( $c, $x, 0 );
		} elseif ( $h < 120 ) {
			$rgb = array( $x, $c, 0 );
		} elseif ( $h < 180 ) {
			$rgb = array( 0, $c, $x );
		} elseif ( $h < 240 ) {
			$rgb = array( 0, $x, $c );
		} elseif ( $h < 300 ) {
			$rgb = array( $x, 0, $c );
		} else {
			$rgb = array( $c, 0, $x );
		}

		$hex = '#';

		foreach ( $rgb as $channel ) {
			$hex .= str_pad( dechex( (int) round( ( $channel + $m ) * 255 ) ), 2, '0', STR_PAD_LEFT );
		}

		return $hex;

	}

	/**
	 * Build a lighter, less saturated version of a color.
	 *
	 * Used to turn a branding accent colour — which is often very dark — into
	 * something readable as a button background with white text.
	 *
	 * @param string $hex_color Color in hex format.
	 * @param float  $lightness Target lightness, 0-1.
	 * @param string $fallback  Returned when the color has no usable hue.
	 * @return string Color in hex format.
	 */
	public function soften( $hex_color, $lightness = 0.80, $fallback = '#f2b3ad' ) {

		$hsl = $this->hex_to_hsl( $hex_color );

		// A greyscale accent has no hue to build on, so there's nothing to soften.
		if ( $hsl[1] < 0.05 ) {
			return $fallback;
		}

		$saturation = min( max( $hsl[1], 0.35 ), 0.65 );

		return $this->hsl_to_hex( $hsl[0], $saturation, $lightness );

	}

	/**
	 * Pick a text color that stays readable on the provided background.
	 *
	 * Keeps the background's own hue so the pairing still looks deliberate,
	 * darkening it until it clears the WCAG AA ratio for normal text.
	 *
	 * @param string $background Background color in hex format.
	 * @param string $fallback   Returned when no shade of the hue is readable.
	 * @return string Color in hex format.
	 */
	public function readable_text_color( $background, $fallback = '#1d2327' ) {

		$hsl       = $this->hex_to_hsl( $background );
		$lightness = $hsl[2];

		while ( $lightness > 0 ) {
			$lightness = max( $lightness - 0.04, 0 );
			$candidate = $this->hsl_to_hex( $hsl[0], $hsl[1], $lightness );

			if ( $this->contrast_ratio( $candidate, $background ) >= 4.5 ) {
				return $candidate;
			}
		}

		return $fallback;

	}

	/**
	 * Build a darker version of a color, for hover and active states.
	 *
	 * @param string $hex_color Color in hex format.
	 * @param float  $amount    How much lightness to remove, 0-1.
	 * @return string Color in hex format.
	 */
	public function darken( $hex_color, $amount = 0.08 ) {

		$hsl = $this->hex_to_hsl( $hex_color );

		return $this->hsl_to_hex( $hsl[0], $hsl[1], max( $hsl[2] - $amount, 0 ) );

	}

	/**
	 * Get the WCAG relative luminance of the provided color.
	 *
	 * @param string $hex_color Color in hex format.
	 * @return float Relative luminance, 0-1.
	 */
	public function relative_luminance( $hex_color ) {

		$rgb       = $this->hex_to_rgb( $hex_color );
		$channels  = array();

		foreach ( $rgb as $value ) {
			$value      = $value / 255;
			$channels[] = $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
		}

		return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];

	}

	/**
	 * Get the contrast ratio between the provided color and white.
	 *
	 * @param string $hex_color Color in hex format.
	 * @return float Contrast ratio, 1-21.
	 */
	public function contrast_with_white( $hex_color ) {

		return $this->contrast_ratio( $hex_color, '#ffffff' );

	}

	/**
	 * Get the contrast ratio between two colors.
	 *
	 * @param string $hex_a First color in hex format.
	 * @param string $hex_b Second color in hex format.
	 * @return float Contrast ratio, 1-21.
	 */
	public function contrast_ratio( $hex_a, $hex_b ) {

		$lum_a = $this->relative_luminance( $hex_a );
		$lum_b = $this->relative_luminance( $hex_b );

		$lighter = max( $lum_a, $lum_b );
		$darker  = min( $lum_a, $lum_b );

		return ( $lighter + 0.05 ) / ( $darker + 0.05 );

	}

}
