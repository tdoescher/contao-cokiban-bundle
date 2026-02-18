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
use tdoescher\CokibanBundle\Service\CokibanContext;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements GlobalsInterface
{
    protected $cokiban = [];

    public function __construct(private readonly CokibanContext $cokibanContext)
    {
        $this->cokiban = $this->cokibanContext->getConfig();
    }

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
        if (empty($this->cokiban)) {
            return [];
        }

        return [
            'cokiban' => [
                'template' => empty($this->cokiban['template']) ? '@Contao/page/_cokiban.html.twig' : '@Contao/' . $this->cokiban['template'] . '.html.twig',
                'id' => $this->cokiban['id'],
                'version' => $this->cokiban['version'],
                'days' => $this->cokiban['days'],
                'active' => $this->cokiban['active'],
                'google_consent_mode' => $this->cokiban['google_consent_mode'],
                'groups' => $this->cokiban['groups'],
                'translation' => $this->cokiban['translation'],
                'config' => $this->cokiban['id'] . ',' . $this->cokiban['version'] . ',' . $this->cokiban['days'] . ',' . ($this->cokiban['active'] ? '1' : '0') . ',' . ($this->cokiban['google_consent_mode'] ? '1' : '0'),
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

        if (!isset($this->cokiban['templates'][$templateName]) || !isset($this->cokiban['translation']['replacements'][$templateName])) {
            return;
        }

        $replacementTemplate = empty($this->cokiban['translation']['replacements'][$templateName]['template'])
            ? 'content_element/cokiban_replacement'
            : $this->cokiban['translation']['replacements'][$templateName]['template'];

        $context['background'] = false;
        $context['button'] = $this->cokiban['translation']['replacements'][$templateName]['button'];
        $context['template'] = $replacementTemplate;
        $context['text'] = $this->cokiban['translation']['replacements'][$templateName]['text'];

        if (isset($this->cokiban['translation']['replacements'][$templateName]['background'])) {
            if (preg_match('/\\b[0-9a-f]{8}\\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\\b[0-9a-f]{12}\\b/u', $this->cokiban['translation']['replacements'][$templateName]['background'])) {
                $backgroundFile = FilesModel::findById($this->cokiban['translation']['replacements'][$templateName]['background']);
                $context['background'] = ($backgroundFile !== null) ? $backgroundFile->path : null;
            } else {
                $context['background'] = $this->cokiban['translation']['replacements'][$templateName]['background'];
            }
        }

        $replacement = $environment->render('@Contao/' . $replacementTemplate . '.html.twig', $context);

        return '<template data-x-data="cokibanReplacement" data-x-bind="bind" data-cokiban-cookies="' . implode(',', $this->cokiban['templates'][$templateName]) . '">' . $replacement . '</template>';
    }
}
