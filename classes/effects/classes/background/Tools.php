<?php

/**
 * Limit
 *
 * Constrain a numeral to fall between two values
 *
 * @access	public
 * @param	mixed	$val	value to constrain
 * @param	mixed	$c1		first contraint
 * @param	mixed	$c2		second constraint
 * @return	mixed			constrained value
 */
function limit($val, $c1, $c2) {
	return min(max($val, min($c1, $c2)), max($c1, $c2));
}

/**
 * ImageFlipHorizontal
 *
 * Flip an image horizontally
 *
 * @access	public
 * @param	image	$old	image resource for original image
 * @return	void
 */
function imageFlipHorizontal(&$old)
{
	$w = imagesx($old);
	$h = imagesy($old);
	$new = imagecreatetruecolor($w, $h);
	imagealphablending($new, false);
	for ($x = 0; $x < $w; $x++)
		imagecopy($new, $old, $x, 0, $w - $x - 1, 0, 1, $h);
	$old = $new;
}

/**
 * ImageFlipVertical
 *
 * Flip an image vertically
 *
 * @access	public
 * @param	image	$old	image resource for original image
 * @return	void
 */
function imageFlipVertical(&$old)
{
	$w = imagesx($old);
	$h = imagesy($old);
	$new = imagecreatetruecolor($w, $h);
	imagealphablending($new, false);
	for ($y = 0; $y < $h; $y++)
		imagecopy($new, $old, 0, $y, 0, $h - $y - 1, $w, 1);
	$old = $new;
}

/**
 * Area
 *
 * Given a value for x = n, computes the area under a circular arc
 * from x = 0 -> n, with the cirle centerd at the orgin
 *
 * @access	public
 * @param	int		$x	x-coordinate for the pixel
 * @param	int		$r	radius of the arc
 * @return	float	area under the arc
 */
function area($x, $r)
{
	return ($x * loc($x, $r) + $r * $r * asin($x / $r)) / 2;
}

/**
 * IsInside
 *
 * Helper method to determine if a coordinate lies inside
 * of the arc.
 *
 * @access	public
 * @param	int		$x	x-coordinate
 * @param	int		$y	y-coordinate
 * @param	int		$r	radius of the arc
 * @return	bool	true if coordinate lies inside bounds of arc
 */
function isInside($x, $y, $r)
{
	return $x * $x + $y * $y <= $r * $r;
}

/**
 * LawOfCosines (loc)
 *
 * Used to calculate length of opposite side
 * of a right triangle, given the length of the
 * hypotenuse and one side.
 *
 * @access	public
 * @param	int		$xy		Length of either side of the right triangle
 * @param	int		$h		Length of the hypotenuse
 * @return	int		Length of the unknown side
 */
function loc($xy, $r)
{
	return sqrt($r * $r - $xy * $xy);
}

?>