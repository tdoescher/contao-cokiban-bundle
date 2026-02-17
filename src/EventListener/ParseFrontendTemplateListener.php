<?php

/**
 * This file is part of CokibanBundle for Contao
 *
 * @package   tdoescher/cokiban-bundle
 * @author    Torben Döscher <mail@tdoescher.de>
 * @license   LGPL
 * @copyright   tdoescher.de // WEB & IT <https://tdoescher.de>
 */

namespace tdoescher\CokibanBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Controller;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use tdoescher\CokibanBundle\Service\CokibanContext;

#[AsHook('parseFrontendTemplate', priority: 100)]
class ParseFrontendTemplateListener
{
    protected $cokiban = [];

    public function __construct(private readonly CokibanContext $cokibanContext)
    {
        $this->cokiban = $cokibanContext->getConfig();
    }

    public function __invoke(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        $cokiban = $this->cokibanContext->getConfig();

        if (!isset($this->cokiban) || !$this->cokiban || $buffer === '') {
            return $buffer;
        }

        if ($templateName === 'fe_page') {
            $cokibanTemplate = 'cokiban';

            if (isset($this->cokiban['template']) && $this->cokiban['template'] !== '') {
                $cokibanTemplate = $this->cokiban['template'];
            }

            $objTemplate = new FrontendTemplate($cokibanTemplate);

            $objTemplate->id = $this->cokiban['id'];
            $objTemplate->version = $this->cokiban['version'];
            $objTemplate->days = $this->cokiban['days'];
            $objTemplate->active = $this->cokiban['active'];
            $objTemplate->googleConsentMode = $this->cokiban['google_consent_mode'];
            $objTemplate->groups = $this->cokiban['groups'];
            $objTemplate->translation = $this->cokiban['translation'];
            $objTemplate->config = $objTemplate->id.','.$objTemplate->version.','.$objTemplate->days.','.($this->cokiban['active'] ? '1' : '0').','.($this->cokiban['google_consent_mode'] ? '1' : '0');
            $objTemplate->cookies = implode(',', $this->cokiban['cookies']);

            return preg_replace("/<body([^>]*)>/is", '<body$1>' . $objTemplate->parse(), $buffer);
        }

        if (isset($this->cokiban['templates'][$templateName])) {
            $buffer = '<template data-x-data="cokibanTemplate" data-x-bind="bind" data-cokiban-cookies="' . implode(',', $this->cokiban['templates'][$templateName]) . '">' . $buffer . '</template>';

            if (isset($this->cokiban['translation']['replacements'][$templateName])) {
                $replacementTemplate = 'ce_cokiban_replacement';

                if (isset($this->cokiban['translation']['replacements'][$templateName]['template']) && $this->cokiban['translation']['replacements'][$templateName]['template'] !== '') {
                    $replacementTemplate = $this->cokiban['translation']['replacements'][$templateName]['template'];
                }

                $objTemplate = new FrontendTemplate($replacementTemplate);
                $objTemplate->setData($template->getData());
                $objTemplate->class = $replacementTemplate;
                $objTemplate->replacementButton = $this->cokiban['translation']['replacements'][$templateName]['button'];
                $objTemplate->replacementText = $this->cokiban['translation']['replacements'][$templateName]['text'];

                if (isset($this->cokiban['translation']['replacements'][$templateName]['background'])) {
                    if (preg_match('/\\b[0-9a-f]{8}\\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\\b[0-9a-f]{12}\\b/u', $this->cokiban['translation']['replacements'][$templateName]['background'])) {
                        $background = FilesModel::findById($this->cokiban['translation']['replacements'][$templateName]['background']);

                        if ($background !== null) {
                            $objTemplate->replacementBackground = $background->path;
                        }
                        else {
                            $objTemplate->replacementBackground = null;
                        }
                    }
                    else {
                        $objTemplate->replacementBackground = $this->cokiban['translation']['replacements'][$templateName]['background'];
                    }
                }

                $buffer = '<template data-x-data="cokibanReplacement" data-x-bind="bind" data-cokiban-cookies="' . implode(',', $this->cokiban['templates'][$templateName]) . '">' . $objTemplate->parse() . '</template>' . $buffer;
            }
        }

        return $buffer;
    }
}
