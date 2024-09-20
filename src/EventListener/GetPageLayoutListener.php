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
use Contao\PageRegular;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\System;

#[AsHook('getPageLayout', priority: 100)]
class GetPageLayoutListener
{
  public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
  {
    $container = System::getContainer();
    $config = System::getContainer()->getParameter('cokiban');

    $rootPage = PageModel::findByIdOrAlias($pageModel->rootAlias);
    $rootPageAlias = str_replace('-', '_', $rootPage->alias);

    if(!is_array($config['translations']))
    {
      return;
    }

    if(isset($config['banners'][$rootPageAlias])) {
      $GLOBALS['TL_COKIBAN'] = $config['banners'][$rootPageAlias];
    } else if(isset($config['banners']['global'])) {
      $GLOBALS['TL_COKIBAN'] = $config['banners']['global'];
    } else {
      return;
    }

    $GLOBALS['TL_COKIBAN']['id'] = $GLOBALS['TL_COKIBAN']['id'] ?? $rootPage->id;

    if(!isset($GLOBALS['TL_COKIBAN']['groups'])) $GLOBALS['TL_COKIBAN']['groups'] = [];
    if(!isset($GLOBALS['TL_COKIBAN']['cookies'])) $GLOBALS['TL_COKIBAN']['cookies'] = [];
    if(!isset($GLOBALS['TL_COKIBAN']['templates'])) $GLOBALS['TL_COKIBAN']['templates'] = [];

    foreach($GLOBALS['TL_COKIBAN']['groups'] as $groupKey => $group)
    {
      $GLOBALS['TL_COKIBAN']['cookies'][] = $groupKey;

      foreach($group as $cookieKey => $cookie)
      {
        $GLOBALS['TL_COKIBAN']['cookies'][] = $groupKey.ucfirst($cookieKey);

        foreach($cookie as $tempalte)
        {
          $GLOBALS['TL_COKIBAN']['templates'][$tempalte][] = $groupKey.ucfirst($cookieKey);
        }
      }
    }

    if(in_array($pageModel->id, $GLOBALS['TL_COKIBAN']['pages']) || in_array($pageModel->alias, $GLOBALS['TL_COKIBAN']['pages']))
    {
      $GLOBALS['TL_COKIBAN']['active'] = false;
    }
    else
    {
      $GLOBALS['TL_COKIBAN']['active'] = true;
    }

    if(isset($config['translations'][$rootPage->language]))
    {
      $GLOBALS['TL_LANG']['cokiban'] = $config['translations'][$rootPage->language];
    }
    else
    {
      $GLOBALS['TL_LANG']['cokiban'] = reset($config['translations']);
    }
  }
}
