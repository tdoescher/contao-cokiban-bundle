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
use Contao\Controller;
use Contao\FilesModel;
use Contao\FrontendTemplate;

/**
 * @Hook("parseFrontendTemplate")
 */
class ParseFrontendTemplateListener
{
    public function __invoke(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        if(!$GLOBALS['TL_COKIBAN'] || $buffer === '')
        {
            return $buffer;
        }

        if($templateName === 'fe_page')
        {
            $template = 'cokiban';

            if(isset($GLOBALS['TL_COKIBAN']['template']) && $GLOBALS['TL_COKIBAN']['template'] !== '') {
                $template = $GLOBALS['TL_COKIBAN']['template'];
            }

            $objTemplate = new FrontendTemplate($template);

            $objTemplate->id = $GLOBALS['TL_COKIBAN']['id'];
            $objTemplate->name = 'cokiban_store_'.$objTemplate->id;
            $objTemplate->version = $GLOBALS['TL_COKIBAN']['version'];
            $objTemplate->days = $GLOBALS['TL_COKIBAN']['days'];
            $objTemplate->active = $GLOBALS['TL_COKIBAN']['active'];
            $objTemplate->groups = $GLOBALS['TL_COKIBAN']['groups'];
            $objTemplate->cookies = $GLOBALS['TL_COKIBAN']['cookies'];
            $objTemplate->translation = $GLOBALS['TL_LANG']['cokiban'];

            $init = [
                'id' => $objTemplate->id,
                'name' => $objTemplate->name,
                'version' => $objTemplate->version,
                'days' => $objTemplate->days,
                'active' => $objTemplate->active,
                'cookies' => $objTemplate->cookies
            ];

            $objTemplate->init = str_replace('"', '\'', json_encode($init));

            return preg_replace("/<body([^>]*)>/is", '<body$1>'.$objTemplate->parse(), $buffer);
        }

        if(isset($GLOBALS['TL_COKIBAN']['templates'][$templateName]))
        {
            $buffer = '<template x-data x-if="$store.cokiban.valid.'.implode(' && $store.cokiban.valid.', $GLOBALS['TL_COKIBAN']['templates'][$templateName]).'">'.$buffer.'</template>';

            if(isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]))
            {
                $template = 'ce_cokiban_replacement';

                if(isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template']) && $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template'] !== '') {
                    $template = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['template'];
                }

                $objTemplate = new FrontendTemplate($template);
                $objTemplate->class = $template;
                $objTemplate->button = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['button'];
                $objTemplate->text = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['text'];

                if(isset($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background']))
                {
                    if(preg_match('/\\b[0-9a-f]{8}\\b-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-\\b[0-9a-f]{12}\\b/u', $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background']))
                    {
                        $background = FilesModel::findById($GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background']);

                        if($background !== null)
                        {
                            $objTemplate->background = $background->path;
                        }
                        else
                        {
                            $objTemplate->background = null;
                        }
                    }
                    else
                    {
                        $objTemplate->background = $GLOBALS['TL_LANG']['cokiban']['replacements'][$templateName]['background'];
                    }
                }

                $buffer = '<template x-data x-if="!($store.cokiban.valid.'.implode(' && $store.cokiban.valid.', $GLOBALS['TL_COKIBAN']['templates'][$templateName]).')">'.$objTemplate->parse().'</template>'.$buffer;
            }

            return $buffer;
        }

        return $buffer;
    }
}
