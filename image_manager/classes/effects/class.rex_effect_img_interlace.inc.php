<?php
/**
* JPG interlace - ImageManager Effect
*
* @package redaxo4.3
* @version 0.1
* @link http://rexdev.de/addons/image_manager-ep.html
* @link http://svn.rexdev.de/redmine/projects/image-manager-ep
* $Id$:
*/

class rex_effect_img_interlace extends rex_effect_abstract
{

  function execute()
  {
    global $REX;
    
    switch($this->image->img['format'])
    {
      case 'JPG';
      if($this->params['jpg_interlace']=='on')
       $this->image->img['interlace'] = true;

      case 'PNG';
        if($this->params['png_interlace']=='on')
          $this->image->img['interlace'] = true;

      case 'GIF';
        if($this->params['png_interlace']=='on')
          $this->image->img['interlace'] = true;

      default:
        $this->image->img['interlace'] = false;
    }
  }

  function getParams()
  {
    global $REX;

    return array(
      array(
        'label' => 'JPG interlace',
        'name' => 'jpg_interlace',
        'type'  => 'select',
        'options' => array(
                     'on',
                     'off'),
        'default' => 'off'
      ),
      array(
        'label' => 'PNG interlace',
        'name' => 'png_interlace',
        'type'  => 'select',
        'options' => array(
                     'on',
                     'off'),
        'default' => 'off'
      ),
      array(
        'label' => 'GIF interlace',
        'name' => 'gif_interlace',
        'type'  => 'select',
        'options' => array(
                     'on',
                     'off'),
        'default' => 'off'
      )
    );
  }

}