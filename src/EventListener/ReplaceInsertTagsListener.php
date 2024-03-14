<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('replaceInsertTags', priority: 100)]
class ReplaceInsertTagsListener
{
  public function __invoke(string $insertTag)
  {
    if(!isset($GLOBALS['TL_COKIBAN']))
    {
      return false;
    }

    $list = explode('::', $insertTag);
    $insertTag = $list[0];
    $value = isset($list[1]) ? $list[1] : false;

    if(!in_array($insertTag, ['cokiban', 'cokiban_open', 'cokiban_close']))
    {
      return false;
    }

    $text = $GLOBALS['TL_LANG']['cokiban']['button']['text'];
    $title = $GLOBALS['TL_LANG']['cokiban']['button']['title'];
    $class = $value ? 'class="'.$value.'" ' : null;

    if($insertTag === 'cokiban')
    {
      return '<a href="#" '.$class.'titel="'.$title.'" data-x-data data-x-bind="$store.cokiban.bindOpenBanner">'.$text.'</a>';
    }

    if($insertTag === 'cokiban_open')
    {
      return '<a href="#" '.$class.'titel="'.$title.'" data-x-data data-x-bind="$store.cokiban.bindOpenBanner">';
    }

    if($insertTag === 'cokiban_close')
    {
      return '</a>';
    }
  }
}
