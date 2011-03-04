<?php

# Require Tools class
require_once 'Tools.php';

/**
 * Class used to convert hex codes to hexidecimal rgb values
 * and store opacity values
 *
 * Use:
 *  $color = new Color('FFAAEE', 0.3);
 *  $resrc = $color->getColorResource($image);
 */
class Color
{
	public $hex,			# original hex code
		   $red = 0,		# red hexidecimal value
		   $green = 0,		# green hexidecimal value
		   $blue = 0,		# blue hexidecimal value
		   $opacity = 1;	# opacity
	
	/**
	 * Constructor for the Color object.
	 *
	 * @access	public
	 * @param	string	$hex		3 or 6 character hex code
	 * @param	float	$opacity	decimal from 0 to 1
	 * @return	void
	 */
	public function __construct($hex, $opacity = 1) {
		$this->hex = preg_replace('/[^a-fA-F0-9]+/', '', $hex);
		
		if (preg_match('/^([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])$/', $this->hex, $m)) {
			$this->red = hexdec($m[1] . $m[1]);
			$this->green = hexdec($m[2] . $m[2]);
			$this->blue = hexdec($m[3] . $m[3]);
		} else if (preg_match('/^([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})$/', $this->hex, $m)) {
			$this->red = hexdec($m[1]);
			$this->green = hexdec($m[2]);
			$this->blue = hexdec($m[3]);
		}
		
		$this->opacity = limit($opacity, 0, 1);
	}
	
	/**
	 * GetColorResource
	 *
	 * Retreives allocated color object for an image
	 *
	 * @access	public
	 * @param	image	$image	image resource created by imagecreatetruecolor
	 * @return	color	color resource allocated for supplied image
	 */
	public function getColorResource($image) {
		return imagecolorallocatealpha($image, $this->red, $this->green, $this->blue, 127 * (1 - $this->opacity));
	}
}

?>