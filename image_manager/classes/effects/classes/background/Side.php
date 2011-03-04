<?php
# Require RGB, Corner and Tools classes
require_once 'Color.php';
require_once 'Corner.php';
require_once 'Tools.php';


/**
 * Class used to create rounded side images with optional borders
 *
 * Use:
 *  $params = array(
 *  	'radius'	=> 15,
 * 		'side'		=> 'left',
 *		'height'	=> 400
 *  );
 *  $img = Side::create($params);
 *  header('Content-Type: image/png');
 *  imagepng($img);
 */
class Side
{
	private $width = 100,				# width of rectangle
			$height = 100,				# height of rectangle
			$radius = 10,				# radius of corner
			$borderwidth = 0,			# width of border
			$side = 'top',				# side of the rectangle to generate
			$antialias = true;			# antialias flag
	
	/**
	 * Constructor for the Side object.
	 *
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- width			: {2, 3, ... , n}
	 *								- height		: {2, 3, ... , n}
	 *								- radius		: {1, 2, ... , n}
	 *								- borderwidth	: {0, 1, ... , n}
	 *								- side			: side of the rectangle to render {'r', 'l', 't', 'b'}
	 *								- antialias		: {true, false}
	 *								- colors		: array of color objects [foreground, border, background]
	 * @return	void
	 */
	public function __construct($params)
	{
		if (is_array($params))
			foreach($params as $param => $value)
				$this->{$param} = $value;
		
		$this->width = max($this->width, 1);
		$this->height = max($this->height, 1);
		$this->side = strtolower($this->side);
		
		switch ($this->side) {
			case 'l' :
			case 'left' :
			case 'r' :
			case 'right' :
				$this->width = $this->radius = limit($this->radius, 1, floor($this->height / 2));
				break;
			case 't' :
			case 'top' :
			case 'b' :
			case 'bottom' :
			default :
				$this->height = $this->radius = limit($this->radius, 1, floor($this->width / 2));
				break;
		}
		
		$this->borderwidth = limit($this->borderwidth, 0, $this->radius);
	}
	
	/**
	 * Image
	 *
	 * Used to build the actual image resource.
	 *
	 * @access	public
	 * @return	image resource for rounded rectangle side
	 */
	public function image()
	{
		$this->image = imagecreatetruecolor($this->width, $this->height);
		imagealphablending($this->image, false);
		
		$color = $this->colors['foreground']->getColorResource($this->image);
		imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $color);
		
		if ($this->borderwidth > 0) {
			$color = $this->colors['border']->getColorResource($this->image);
			
			switch ($this->side) {
				case 'l' :
				case 'left' :
					imagefilledrectangle($this->image, 0, 0, $this->borderwidth - 1, $this->height - 1, $color);
					break;
				case 'r' :
				case 'right' :
					imagefilledrectangle($this->image, $this->width - $this->borderwidth, 0, $this->width - 1, $this->height - 1, $color);
					break;
				case 'b' :
				case 'bottom' :
					imagefilledrectangle($this->image, 0, $this->height - $this->borderwidth, $this->width - 1, $this->height - 1, $color);
					break;
				case 't' :
				case 'top' :
				default :
					imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->borderwidth - 1, $color);
					break;
			}
		}
		
		$params = array(
			'radius'		=> $this->radius,
			'orientation'	=> 'tl',
			'colors'		=> $this->colors,
			'borderwidth'	=> $this->borderwidth,
			'antialias'		=> $this->antialias
		);
		
		$img = Corner::create($params);
		
		if ($this->side == 't' || $this->side == 'top' || $this->side == 'l' || $this->side == 'left')
			imagecopy($this->image, $img, 0, 0, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		
		if ($this->side == 'l' || $this->side == 'left' || $this->side == 'b' || $this->side == 'bottom')
			imagecopy($this->image, $img, 0, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipHorizontal($img);
		
		if ($this->side == 'b' || $this->side == 'bottom' || $this->side == 'r' || $this->side == 'right')
			imagecopy($this->image, $img, $this->width - $this->radius, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		
		if ($this->side == 'r' || $this->side == 'right' || $this->side == 't' || $this->side == 'top')
			imagecopy($this->image, $img, $this->width - $this->radius, 0, 0, 0, $this->radius, $this->radius);
		
		imagedestroy($img);
		
		return $this->image;
	}
	
	/**
	 * Create
	 *
	 * Method used as a factory for rectangle side images.
	 * Offers a quick way to send parameters and return
	 * an image resource for output.
	 *
	 * @static
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- (See constructor docs for accepted values)
	 * @return	image resource for generated side image
	 */
	public static function create($params)
	{
		$s = new Side($params);
		return $s->image();
	}
}
?>