<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use tdoescher\CokibanBundle\CokibanBundle;

class Plugin implements BundlePluginInterface
{
  public function getBundles(ParserInterface $parser): array
  {
    $GLOBALS['TL_COKIBAN'] = false;

    return [
      BundleConfig::create(CokibanBundle::class)
        ->setLoadAfter([ContaoCoreBundle::class])
        ->setReplace(['cokiban']),
    ];
  }
}
