<?php
/**
* Background (aka RoundedPHP) - ImageManager Effect
*
* @package redaxo4.3
* @version 0.1
* @link http://svn.rexdev.de/redmine/projects/image-manager-ep
* $Id$:
*/

/**
 * Rounded PHP, Rounded corners made easy.
 *
 * rounded.php
 *
 * PHP version 5, GD version 2
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 * 
 * @category	Rounded PHP
 * @package		<none>
 * @author		Nevada Kent <support@nak5.com>
 * @version		2.0
 * @link		http://nak5.com
 * @link		http://nak5.com/demos/roundedphp/
 * @link		http://nak5.com/projects/roundedphp/
 */

class rex_effect_background extends rex_effect_abstract
{

  function execute()
  {
    // FORCE NO ERROR DISPLAY (WOULD MESS UP CONTENT_LENGTH -> BROKEN IMAGE)
    ////////////////////////////////////////////////////////////////////////////
    @ini_set('error_reporting',0);
    @ini_set('display_errors',0);

    // ROUNDEDPHP CLASSES
    ////////////////////////////////////////////////////////////////////////////
    require_once 'classes/background/Color.php';
    require_once 'classes/background/Corner.php';
    require_once 'classes/background/Rectangle.php';
    require_once 'classes/background/Side.php';

    $gdimage      =& $this->image->getImage();
    $format       = $this->params['f'];
    $shape        = $this->params['sh'];

    $this->image->img['file'] = 'BG_'.md5(serialize($this->params)).'.'.$format;
    $this->image->img['width']  = $this->params['w'];
    $this->image->img['height'] = $this->params['h'];
    $this->image->img['format'] = strtoupper($format);

    switch (strtolower($format)) {
      case 'jpg' :
      case 'jpeg' :
        $transparentcolor = NULL;
      case 'gif' :
        $backgroundopacity = 100;
        $borderopacity = 100;
        $foregroundopacity = 100;
        break;
      case 'png' :
        $transparentcolor = NULL;
        break;
    }

    $params = array(
      'radius'        => $this->params['r'],
      'width'         => $this->params['w'],
      'height'        => $this->params['h'],
      'borderwidth'   => $this->params['bw'],
      'orientation'   => $this->params['o'],
      'side'          => $this->params['si'],
      'antialias'     => $this->params['aa'],
      'colors'        => array(
        'foreground'  => new Color($this->params['fgc'], $this->params['fgo'] / 100),
        'border'      => new Color($this->params['bc'],  $this->params['bo']  / 100),
        'background'  => new Color($this->params['bgc'], $this->params['bgo'] / 100)
      )
    );

    switch (strtolower($shape)) {
      case 'r' :
      case 'rect' :
      case 'rectangle' :
        $img = Rectangle::create($params);
        break;
      case 's' :
      case 'side' :
        $img = Side::create($params);
        break;
      case 'c' :
      case 'corner' :
      default :
        $img = Corner::create($params);
        break;
    }

    imagesavealpha($img, true);
    
    if (!is_null($transparentcolor) && $transparentcolor) {
      $color = new Color($transparentcolor);
      imagecolortransparent($img, $color->getColorResource($img));
    }

    $gdimage = $img;
  }

  function getParams()
  {
    global $REX;

    return array(
      array(
        'label'=> 'Shape',
        'name' => 'sh',
        'type' => 'select',
        'options' => array(
                     'corner',
                     'rectangle',
                     'side'),
        'default' => 'rectangle',
      ),
      array(
        'label'=> 'Orientation',
        'name' => 'o',
        'type' => 'select',
        'options' => array(
                     'tl',
                     'tr',
                     'br',
                     'bl'),
        'default' => 'tl',
      ),
      array(
        'label'=> 'Side',
        'name' => 'si',
        'type' => 'select',
        'options' => array(
                     'top',
                     'right',
                     'bottom',
                     'left'),
        'default' => 'top',
      ),
      array(
        'label'=> 'Antialias',
        'name' => 'aa',
        'type' => 'select',
        'options' => array(
                     0,
                     1),
        'default' => 1,
      ),
      array(
        'label'=> 'Format',
        'name' => 'f',
        'type' => 'select',
        'options' => array(
                     'png',
                     'gif',
                     'jpg'),
        'default' => 'png',
      ),
      array(
        'label'=> 'Radius',
        'name' => 'r',
        'type' => 'int',
        'default' => 20,
      ),
      array(
        'label'=> 'Width',
        'name' => 'w',
        'type' => 'int',
        'default' => 400,
      ),
      array(
        'label'=> 'Height',
        'name' => 'h',
        'type' => 'int',
        'default' => 300,
      ),
      array(
        'label'=> 'Foreground Color',
        'name' => 'fgc',
        'type' => 'string',
        'default' => '2ba3e3',
      ),
      array(
        'label'=> 'Foreground Opacity',
        'name' => 'fgo',
        'type' => 'select',
        'options' => $this->select_vals(0,100),
        'default' => 50,
      ),
      array(
        'label'=> 'Background Color',
        'name' => 'bgc',
        'type' => 'string',
        'default' => 'FF0000',
      ),
      array(
        'label'=> 'Background Opacity',
        'name' => 'bgo',
        'type' => 'select',
        'options' => $this->select_vals(0,100),
        'default' => 0,
      ),
      array(
        'label'=> 'Border Width',
        'name' => 'bw',
        'type' => 'int',
        'default' => 1,
      ),
      array(
        'label'=> 'Border Color',
        'name' => 'bc',
        'type' => 'string',
        'default' => '000000',
      ),
      array(
        'label'=> 'Border Opacity',
        'name' => 'bo',
        'type' => 'select',
        'options' => $this->select_vals(0,100),
        'default' => 100,
      ),
      array(
        'label'=> 'Transparent Color',
        'name' => 'tc',
        'type' => 'string',
        'default' => 'FFFFFF',
      )
     );
  }

  function select_vals($s,$e)
  {
    $sel = array();
    for($i=$s;$i<=$e;++$i)
    {
      $sel[$i]=$i;
    }
    return $sel;
  }

  // ROUNDEDPHP FUNCTIONS
  //////////////////////////////////////////////////////////////////////////////

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

}