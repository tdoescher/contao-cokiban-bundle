<?php

/**
 * This file is part of Cokiban Bundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de - WEB und IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;

/**
 * @Hook("replaceInsertTags")
 */
class ReplaceInsertTagsListener
{
    public function __invoke(string $tag)
    {
        if(!isset($GLOBALS['TL_COKIBAN']))
        {
            return false;
        }

        $list = explode('::', $tag);
        $tag = $list[0];
        $value = isset($list[1]) ? $list[1] : false;

        if(!in_array($tag, ['cokiban', 'cokiban_open', 'cokiban_close']))
        {
            return false;
        }

        $text = $GLOBALS['TL_LANG']['cokiban']['button']['text'];
        $title = $GLOBALS['TL_LANG']['cokiban']['button']['title'];
        $class = $value ? 'class="'.$value.'" ' : null;

        if($tag === 'cokiban')
        {
            return '<a href="#" '.$class.'titel="'.$title.'" x-data @click.prevent="$store.cokiban.openBanner()">'.$text.'</a>';
        }

        if($tag === 'cokiban_open')
        {
            return '<a href="#" '.$class.'titel="'.$title.'" x-data @click.prevent="$store.cokiban.openBanner()">';
        }

        if($tag === 'cokiban_close')
        {
            return '</a>';
        }
    }
}
