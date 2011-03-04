<?php
# Require Corner, RGB and Tools classes
require_once 'Color.php';
require_once 'Corner.php';
require_once 'Tools.php';


/**
 * Class used to create rounded rectangle images with optional borders
 *
 * Use:
 *  $params = array(
 *  	'radius'		=> 15,
 * 		'width'			=> 300,
 *		'height'		=> 500,
 *		'background'	=> 'FF0000'
 *  );
 *  $img = Rectangle::create($params);
 *  header('Content-Type: image/png');
 *  imagepng($img);
 */
class Rectangle
{
	private $width = 100,				# width of rectangle
			$height = 100,				# height of rectangle
			$radius = 10,				# radius of corner
			$borderwidth = 0,			# width of border
			$antialias = true;			# antialias flag
	
	/**
	 * Constructor for the Rectangle object.
	 *
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- width			: {2, 3, ... , n}
	 *								- height		: {2, 3, ... , n}
	 *								- radius		: {1, 2, ... , n}
	 *								- borderwidth	: {0, 1, ... , n}
	 *								- antialias		: {true, false}
	 *								- colors		: array of color objects [foreground, border, background]
	 * @return	void
	 */
	public function __construct($params)
	{
		if (is_array($params))
			foreach($params as $param => $value)
				$this->{$param} = $value;
		
		$this->width = max($this->width, 2);
		$this->height = max($this->height, 2);
		$this->radius = limit($this->radius, 1, floor(min($this->width, $this->height) / 2));
		$this->borderwidth = limit($this->borderwidth, 0, ceil(min($this->width, $this->height) / 2));
	}
	
	/**
	 * Image
	 *
	 * Used to build the actual image resource.
	 *
	 * @access	public
	 * @return	image resource for rounded rectangle
	 */
	public function image()
	{
		$this->image = imagecreatetruecolor($this->width, $this->height);
		imagealphablending($this->image, false);
		
		$color = $this->colors['border']->getColorResource($this->image);
		imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $color);
		
		if ($this->borderwidth < min($this->width, $this->height) / 2) {
			$color = $this->colors['foreground']->getColorResource($this->image);
			imagefilledrectangle($this->image, $this->borderwidth, $this->borderwidth, $this->width - $this->borderwidth - 1, $this->height - $this->borderwidth - 1, $color);
		}
		
		$params = array(
			'radius'		=> $this->radius,
			'orientation'	=> 'tl',
			'colors'		=> $this->colors,
			'borderwidth'	=> $this->borderwidth,
			'antialias'		=> $this->antialias
		);
		
		$img = Corner::create($params);
		imagecopy($this->image, $img, 0, 0, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		imagecopy($this->image, $img, 0, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipHorizontal($img);
		imagecopy($this->image, $img, $this->width - $this->radius, $this->height - $this->radius, 0, 0, $this->radius, $this->radius);
		
		imageFlipVertical($img);
		imagecopy($this->image, $img, $this->width - $this->radius, 0, 0, 0, $this->radius, $this->radius);
		
		imagedestroy($img);
		
		return $this->image;
	}
	
	/**
	 * Create
	 *
	 * Method used as a factory for rectangle images.
	 * Offers a quick way to send parameters and return
	 * an image resource for output.
	 *
	 * @static
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- (See constructor docs for accepted values)
	 * @return	image resource of rounded rectangle
	 */
	public static function create($params)
	{
		$r = new Rectangle($params);
		return $r->image();
	}
}
?>