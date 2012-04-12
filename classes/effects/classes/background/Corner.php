<?php
# Require RGB and Tools classes
require_once 'Color.php';
require_once 'Tools.php';


/**
 * Class used to create rounded corner images with optional borders
 *
 * Use:
 *  $params = array(
 *  	'radius'		=> 15,
 * 		'orientation'	=> 'bl',
 *		'borderwidth'	=> 2
 *  );
 *  $img = Corner::create($params);
 *  header('Content-Type: image/png');
 *  imagepng($img);
 */
class Corner
{
	private $radius = 10,				# radius of corner
			$orientation = 'tl',		# orientation of corner
			$borderwidth = 0,			# width of border
			$antialias = true;			# antialias flag
	
	/**
	 * Constructor for the Corner object.
	 *
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- radius		: {1, 2, ... , n}
	 *								- orientation	: {'tl', 'tr', 'br', 'bl'}
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
		
		$this->radius = max(intval($this->radius), 1);
		$this->borderwidth = limit($this->borderwidth, 0, $this->radius);
		$this->orientation = strtolower($this->orientation);
	}
	
	/**
	 * Image
	 *
	 * Used to build the actual image resource.
	 *
	 * @access	public
	 * @return	image resource for final rounded corner
	 */
	public function image()
	{
		$this->image = imagecreatetruecolor($this->radius, $this->radius);
		imagealphablending($this->image, false);
		
		$this->draw();
		
		switch ($this->orientation) {
			case 'br' :
			case 'rb' :
				break;
			case 'bl' :
			case 'lb' :
				imageFlipHorizontal($this->image);
				break;
			case 'tr' :
			case 'rt' :
				imageFlipVertical($this->image);
				break;
			case 'tl' :
			case 'lt' :
			default :
				imageFlipHorizontal($this->image);
				imageFlipVertical($this->image);
				break;
		}
		
		return $this->image;
	}
	
	/**
	 * Draw
	 *
	 * Draws the arcs on the image. Includes border and
	 * opacity levels.
	 * Always draws quadrant IV of a circle with center
	 * positioned at (0,0).
	 *
	 * @access	private
	 * @return	void
	 */
	private function draw()
	{
		$c = $this->colors['background']->getColorResource($this->image);
		imagefilledrectangle($this->image, 0, 0, $this->radius - 1, $this->radius - 1, $c);
		
		if ($this->borderwidth > 0) {
			$c = $this->colors['border']->getColorResource($this->image);
			imagefilledellipse($this->image, 0, 0, ($this->radius - 1) * 2, ($this->radius - 1) * 2, $c);
			$this->drawAA($this->radius, $this->colors['border'], $this->colors['background']);
		}
		
		if ($this->radius - $this->borderwidth > 0) {
			$c = $this->colors['foreground']->getColorResource($this->image);
			imagefilledellipse($this->image, 0, 0, ($this->radius - $this->borderwidth - 1) * 2, ($this->radius - $this->borderwidth - 1) * 2, $c);
			if ($this->borderwidth > 0)
				$this->drawAA($this->radius - $this->borderwidth, $this->colors['foreground'], $this->colors['border']);
			else
				$this->drawAA($this->radius, $this->colors['foreground'], $this->colors['background']);
		}
	}
	
	/**
	 * DrawAA
	 *
	 * Draws the antialiasing around each arc
	 *
	 * @access	private
	 * @param	int		$r	radius of arc
	 * @param	Color	$c1	Color object inside arc
	 * @param	Color	$c2	Color object outside arc
	 * @return	void
	 */
	private function drawAA($r, $c1, $c2) {
		if (!$this->antialias)
			return;
		
		$px = array_fill(0, $r, array_fill(0, $r, false));
		
		for ($x = 0; $x < $r; $x++) {
			for ($y = ceil(loc($x, $r)) - 1; $y > -1; $y--) {
				if ($px[$x][$y])
					return;
				
				if (isInside($x + 1, $y + 1, $r))
					break;
				
				$color = $this->blendColors($c1, $c2, $this->computeRatio($x, $y, $r));
				$c = $color->getColorResource($this->image);
				
				imagesetpixel($this->image, $x, $y, $c);
				$px[$x][$y] = true;
				
				if ($x <> $y) {
					imagesetpixel($this->image, $y, $x, $c);
					$px[$y][$x] = true;
				}
			}
		}
	}
	
	/**
	 * ComputeRatio
	 *
	 * Determines the ratio of two colors to be blended
	 *
	 * @access	private
	 * @param	int		$x	x-coordinate for the pixel
	 * @param	int		$y	y-coordinate for the pixel
	 * @param	int		$r	radius of the arc
	 * @return	int		value for color ratio (0 <= r <= 1)
	 */
	private function computeRatio($x, $y, $r)
	{
		if (!$this->antialias)
			return 1;
		
		$x_a = min($x + 1, loc($y, $r));
		$x_b = max($x, loc($y + 1, $r));
		return area($x_a, $r) - area($x_b, $r) + $x_b - $x - $y * ($x_a - $x_b);
	}
	
	/**
	 * BlendColors
	 *
	 * Blends 2 colors, giving attention to both
	 * the ratio of color amounts, and the opacity
	 * level of each color
	 *
	 * @access	private
	 * @param	Color	$c1	1st color
	 * @param	Color	$c2	2nd color
	 * @param	float	$r	ratio of blend (0.7 means 70% of color 1)
	 */
	private function blendColors($c1, $c2, $r)
	{
		$o1 = $c1->opacity * $r;
		$o2 = $c2->opacity * (1 - $r);
		$o = $o1 + $o2;
		
		$o_r = $o == 0 ? 0 : $o2 / $o;
		
		$r = str_pad(dechex($c1->red - $o_r * ($c1->red - $c2->red)), 2, '0', STR_PAD_LEFT);
		$g = str_pad(dechex($c1->green - $o_r * ($c1->green - $c2->green)), 2, '0', STR_PAD_LEFT);
		$b = str_pad(dechex($c1->blue - $o_r * ($c1->blue - $c2->blue)), 2, '0', STR_PAD_LEFT);
		
		return new Color($r . $g . $b, $o);
	}
	
	/**
	 * Create
	 *
	 * Method used as a factory for corner images.
	 * Offers a quick way to send parameters and return
	 * an image resource for output.
	 *
	 * @static
	 * @access	public
	 * @param	array	$params	Associative array of custom parameters:
	 *								- (See constructor docs for accepted values)
	 * @return	image	resource for generated rounded corner
	 */
	public static function create($params)
	{
		$c = new Corner($params);
		return $c->image();
	}
}
?>