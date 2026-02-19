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
use tdoescher\CokibanBundle\Service\CokibanContext;

#[AsHook('replaceInsertTags', priority: 100)]
class ReplaceInsertTagsListener
{
    protected array $cokiban = [];

    public function __construct(private readonly CokibanContext $cokibanContext)
    {
        $this->cokiban = $cokibanContext->getConfig();
    }

    public function __invoke(string $insertTag)
    {
        if (!$this->cokiban) {
            return false;
        }

        $list = explode('::', $insertTag);
        $insertTag = $list[0];
        $value = $list[1] ?? false;

        if (!in_array($insertTag, ['cokiban', 'cokiban_open', 'cokiban_close'])) {
            return false;
        }

        $text = $this->cokiban['translation']['button']['text'];
        $title = $this->cokiban['translation']['button']['title'];
        $class = $value ? 'class="' . $value . '" ' : null;

        if ($insertTag === 'cokiban') {
            return '<a href="#" ' . $class . 'title="' . $title . '" data-x-data="cokibanButton" data-x-bind="bind">' . $text . '</a>';
        }

        if ($insertTag === 'cokiban_open') {
            return '<a href="#" ' . $class . 'title="' . $title . '" data-x-data="cokibanButton" data-x-bind="bind">';
        }

        if ($insertTag === 'cokiban_close') {
            return '</a>';
        }
    }
}
