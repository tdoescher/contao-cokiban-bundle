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
use Contao\PageModel;
use Contao\System;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    protected $cokiban = [];

    public function getFunctions()
    {
        return [
            new TwigFunction('cokiban_wrapper_open', [ $this, 'cokibanOpen' ], [ 'needs_context' => true, 'is_safe' => [ 'html' ] ]),
            new TwigFunction('cokiban_wrapper_close', [ $this, 'cokibanClose' ], [ 'needs_context' => true, 'is_safe' => [ 'html' ] ]),
            new TwigFunction('cokiban_replacement', [ $this, 'cokibanReplacement' ], [ 'needs_environment' => true, 'needs_context' => true, 'is_safe' => [ 'html' ] ])
        ];
    }

    public function getGlobals(): array
    {
        $config = System::getContainer()->getParameter('cokiban');
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        $pageModel = $request->attributes->get('pageModel');

        if(!$pageModel) {
            return [];
        }

        $rootPage = PageModel::findByIdOrAlias($pageModel->rootAlias);
        $rootPageAlias = str_replace('-', '_', $rootPage->alias);

        if (isset($config['disable_token']) && isset($_GET[$config['disable_token']])) {
            return [];
        }

        if (!is_array($config['translations'])) {
            return [];
        }

        if (isset($config['banners'][$rootPageAlias])) {
            $this->cokiban = $config['banners'][$rootPageAlias];
            $this->cokiban['id'] = $this->cokiban['id'] ?? $rootPage->id;
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

        if (isset($config['translations'][$rootPage->language])) {
            $this->cokiban['translation'] = $config['translations'][$rootPage->language];
        }
        else {
            $this->cokiban['translation'] = reset($config['translations']);
        }

        $GLOBALS['TL_COKIBAN'] = $this->cokiban;

        return [
            'cokiban' => [
                'id' => $this->cokiban['id'],
                'version' => $this->cokiban['version'],
                'days' => $this->cokiban['days'],
                'active' => $this->cokiban['active'],
                'googleConsentMode' => $this->cokiban['google_consent_mode'],
                'groups' => $this->cokiban['groups'],
                'translation' => $this->cokiban['translation'],
                'config' => $this->cokiban['id'].','.$this->cokiban['version'].','.$this->cokiban['days'].','.($this->cokiban['active'] ? '1' : '0').','.($this->cokiban['google_consent_mode'] ? '1' : '0'),
                'cookies' => implode(',', $this->cokiban['cookies']),
            ]
        ];
    }

    public function cokibanOpen($context)
    {
        $templateName = $context['template'];

        if (!isset($this->cokiban['templates'][$templateName])) {
            return;
        }

        return '<template data-x-data="cokibanTemplate" data-x-bind="bind" data-cokiban-cookies="' . implode(',', $this->cokiban['templates'][$templateName]) . '">';
    }

    public function cokibanClose($context)
    {
        $templateName = $context['template'];

        if (!isset($this->cokiban['templates'][$templateName])) {
            return;
        }

        return '</template>';
    }

    public function cokibanReplacement($environment, $context)
    {
        $templateName = $context['template'];

        if (!isset($this->cokiban['templates'][$templateName]) || !isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName])) {
            return;
        }

        $replacementTemplate = 'content_element/cokiban_replacement';

        if (isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template']) && $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template'] !== '') {
            $replacementTemplate = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template'];
        }

        $context['background'] = false;
        $context['button'] = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['button'];
        $context['template'] = $replacementTemplate;
        $context['text'] = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['text'];

        if (isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'])) {
            if (preg_match('/\\b[0-9a-f]{8}\\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\\b[0-9a-f]{12}\\b/u', $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'])) {
                $backgroundFile = FilesModel::findById($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background']);
                $context['background'] = ($backgroundFile !== null) ? $backgroundFile->path : null;
            } else {
                $context['background'] = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'];
            }
        }

        $replacement = $environment->render('@Contao/' . $replacementTemplate . '.html.twig', $context);

        return '<template data-x-data="cokibanReplacement" data-x-bind="bind" data-cokiban-cookies="' . implode(',', $this->cokiban['templates'][$templateName]) . '">' . $replacement . '</template>';
    }
}
