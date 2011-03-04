<?php
/**
* JPG image quality - ImageManager Effect
*
* @package redaxo4.3
* @version 0.1
* @link http://rexdev.de/addons/image_manager-ep.html
* @link http://svn.rexdev.de/redmine/projects/image-manager-ep
* $Id$:
*/

class rex_effect_jpg_quality extends rex_effect_abstract
{

  function execute()
  {
    global $REX;
    $this->image->img['quality'] = $this->params['quality'];

  }

  function getParams()
  {
    global $REX,$I18N;

    return array(
      array(
        'label' => 'JPG quality',
        'name' => 'quality',
        'type'  => 'int',
        'default' => $REX['ADDON']['image_manager']['jpg_quality']
      )
    );
  }

}