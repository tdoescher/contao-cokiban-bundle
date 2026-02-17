<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CokibanBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
