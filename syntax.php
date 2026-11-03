<?php
/**
 * DataTables plugin: Add DataTables support to DokuWiki
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Giuseppe Di Terlizzi <giuseppe.diterlizzi@gmail.com>
 * @copyright  (C) 2015-2016, Giuseppe Di Terlizzi
 */
class syntax_plugin_datatables extends DokuWiki_Syntax_Plugin
{

    /** @inheritdoc */
    public function getType()
    {
        return 'container';
    }

    /** @inheritdoc */
    public function getAllowedTypes()
    {
        return ['container', 'substition'];
    }

    /** @inheritdoc */
    public function getPType()
    {
        return 'block';
    }

    /** @inheritdoc */
    public function getSort()
    {
        return 195;
    }

    /** @inheritdoc */
    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern(
            '<(?:DATATABLES?|datatables?)\b.*?>(?=.*?</(?:DATATABLES?|datatables?)>)',
            $mode,
            'plugin_datatables'
        );
    }

    /** @inheritdoc */
    public function postConnect()
    {
        $this->Lexer->addExitPattern('</(?:DATATABLES?|datatables?)>', 'plugin_datatables');
    }

    /** @inheritdoc */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {

        switch ($state) {
            case DOKU_LEXER_UNMATCHED:
            case DOKU_LEXER_EXIT:
            case DOKU_LEXER_ENTER:
                return [$state, $match];
        }

        return [];
    }

    /** @inheritdoc */
    public function render($mode, Doku_Renderer $renderer, $data)
    {

        if (empty($data)) return false;
        if ($mode !== 'xhtml') return false;

        /** @var Doku_Renderer_xhtml $renderer */

        list($state, $match) = $data;

        switch ($state) {

            case DOKU_LEXER_ENTER:

                $html5_data = array();
                $xml = @simplexml_load_string(str_replace('>', '/>', $match));

                if (!is_object($xml)) {
                    $xml = simplexml_load_string('<foo />');

                    global $ACT;
                    if ($ACT == 'preview') {
                        msg(
                            sprintf(
                                '<strong>DataTable Plugin</strong> - Malformed tag (<code>%s</code>).' .
                                ' Please check your code!',
                                hsc($match)
                            ),
                            -1
                        );
                    }
                }

                // Preserve the custom default, allowing each table to opt out.
                if (!isset($xml['scroll-x'])) $xml['scroll-x'] = 'true';

                $styles = [];
                foreach ($xml->attributes() as $key => $value) {
                    $value = (string) $value;
                    if (in_array($key, ['plain', 'striped'], true)) {
                        $value = strtolower(trim($value));
                        if (!in_array($value, ['true', 'false'], true)) continue;
                    }
                    if ($key === 'border') {
                        $value = strtolower(trim($value));
                        if (!in_array($value, ['true', 'false'], true)) {
                            // Allow colour names and hex values, never CSS declarations.
                            if (!preg_match('/^(?:[a-z]+|#(?:[a-f0-9]{3}|[a-f0-9]{6}))$/D', $value)) continue;
                            $styles[] = '--dt-border-color: ' . $value;
                        }
                    }
                    if ($key === 'header-background') {
                        // A colour value only, never arbitrary CSS from page text.
                        if (!preg_match('/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $value)) continue;
                        $styles[] = '--dt-header-background: ' . $value;
                    }
                    $html5_data[] = sprintf("data-%s='%s'", $key, hsc($value));
                }

                $style = $styles ? ' style="' . hsc(implode('; ', $styles)) . '"' : '';
                $renderer->doc .= sprintf('<div class="dt-wrapper" %s%s>', implode(' ', $html5_data), $style);
                return true;

            case DOKU_LEXER_EXIT:
                $renderer->doc .= '</div>';
                return true;

            case DOKU_LEXER_UNMATCHED:
                $renderer->doc .= $match;
                return true;
        }

        // should never be reached
        return false;
    }

}
