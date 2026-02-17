<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package     tdoescher/cokiban-bundle
 * @author      Torben Döscher <mail@tdoescher.de>
 * @license     LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\Service;

use Contao\PageModel;
use Contao\System;
use Symfony\Component\HttpFoundation\RequestStack;

class CokibanContext
{
    protected $cokiban = [];

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getConfig(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        if($request === null || $request->attributes->get('_scope') === 'backend') {
            return [];
        }

        if(!$this->cokiban) {
            $config = System::getContainer()->getParameter('cokiban');

            $pageModel = $request->attributes->get('pageModel');
            $rootAlias = str_replace('-', '_', $pageModel->rootAlias);

            if (isset($config['disable_token']) && isset($_GET[$config['disable_token']])) {
                return [];
            }

            if (!is_array($config['translations'])) {
                return [];
            }

            if (isset($config['banners'][$rootAlias])) {
                $this->cokiban = $config['banners'][$rootAlias];
                $this->cokiban['id'] = $this->cokiban['id'] ?? $pageModel->rootId;
            }
            else if (isset($config['banners']['global'])) {
                $this->cokiban = $config['banners']['global'];
                $this->cokiban['id'] = $this->cokiban['id'] ?? 'global';
            }
            else {
                return [];
            }

            if (!isset($this->cokiban['groups'])) $this->cokiban['groups'] = [];
            if (!isset($this->cokiban['cookies'])) $this->cokiban['cookies'] = [];
            if (!isset($this->cokiban['templates'])) $this->cokiban['templates'] = [];

            foreach ($this->cokiban['groups'] as $groupKey => $group) {
                $this->cokiban['cookies'][] = $groupKey;

                foreach ($group as $cookieKey => $cookie) {
                    $this->cokiban['cookies'][] = $groupKey . ucfirst($cookieKey);

                    foreach ($cookie as $tempalte) {
                        $this->cokiban['templates'][$tempalte][] = $groupKey . ucfirst($cookieKey);
                    }
                }
            }

            if (in_array($pageModel->id, $this->cokiban['pages']) || in_array($pageModel->alias, $this->cokiban['pages']) || (isset($config['hide_token']) && isset($_GET[$config['hide_token']]))) {
                $this->cokiban['active'] = false;
            }
            else {
                $this->cokiban['active'] = true;
            }

            if (isset($config['translations'][$pageModel->rootLanguage])) {
                $this->cokiban['translation'] = $config['translations'][$pageModel->rootLanguage];
            }
            else {
                $this->cokiban['translation'] = reset($config['translations']);
            };
        }

        return $this->cokiban;
    }
}
