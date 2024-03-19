<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\Twig;

use Contao\FilesModel;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      new TwigFunction('cokiban_open', [$this, 'cokibanOpen'], ['needs_context' => true, 'is_safe' => ['html']]),
      new TwigFunction('cokiban_close', [$this, 'cokibanClose'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']])
    ];
  }

  public function cokibanOpen($context)
  {
    $templateName = $context['template'];

    if(!isset($GLOBALS['TL_COKIBAN']['templates'][$templateName])) {
      return;
    }

    return '<template data-x-data data-x-if="$store.cokiban.valid.'.implode(' || $store.cokiban.valid.', $GLOBALS['TL_COKIBAN']['templates'][$templateName]).'">';
  }

  public function cokibanClose($environment, $context)
  {
    $templateName = $context['template'];

    if(!isset($GLOBALS['TL_COKIBAN']['templates'][$templateName]) || !isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName])) {
      return '</template>';
    }

    $replacementTemplate = 'content_element/cokiban_replacement';

    if(isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template']) && $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template'] !== '') {
      $replacementTemplate = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template'];
    }

    $context['background'] = false;
    $context['button'] = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['button'];
    $context['template'] = $replacementTemplate;
    $context['text'] = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['text'];

    if(isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'])) {
      if(preg_match('/\\b[0-9a-f]{8}\\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\\b[0-9a-f]{12}\\b/u', $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'])) {
        $backgroundFile = FilesModel::findById($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background']);
        $context['background'] = ($backgroundFile !== null) ? $backgroundFile->path : null;
      }
      else {
        $context['background'] = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'];
      }
    }

    return '</template><template data-x-data data-x-if="!($store.cokiban.valid.'.implode(' && $store.cokiban.valid.', $GLOBALS['TL_COKIBAN']['templates'][$templateName]).')">'.$environment->render('@Contao/'.$replacementTemplate.'.html.twig', $context).'</template>';
  }
}
