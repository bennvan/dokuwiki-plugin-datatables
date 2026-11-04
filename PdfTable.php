<?php

namespace dokuwiki\plugin\datatables;

/** Resolve presentation options without CSS variables or JavaScript for mPDF. */
class PdfTable
{
    public static function style($html)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML('<?xml encoding="utf-8"?><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        if (!$loaded) return $html;

        $xpath = new \DOMXPath($dom);
        $wrapper = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " dt-wrapper ")]')->item(0);
        if (!$wrapper) return $html;

        // Values have already been validated by the syntax renderer.
        $border = $wrapper->getAttribute('data-border');
        $borderStyle = $border === 'false' ? 'border:0;'
            : ($border !== '' ? 'border:1px solid ' . ($border === 'true' ? '#ddd' : $border) . ';' : '');
        $header = $wrapper->getAttribute('data-header-background');
        $striped = $wrapper->getAttribute('data-striped');

        foreach ($wrapper->getElementsByTagName('table') as $table) {
            self::prependStyle($table, 'width:100%;border-collapse:collapse;' . $borderStyle);
            $index = 0;
            foreach ($xpath->query('./thead/tr | ./tbody/tr | ./tfoot/tr | ./tr', $table) as $row) {
                $body = !in_array($row->parentNode->nodeName, ['thead', 'tfoot'], true);
                if ($body) $index++;
                foreach ($xpath->query('./th | ./td', $row) as $cell) {
                    $style = $borderStyle;
                    if ($cell->nodeName === 'th' && $header !== '') {
                        $style .= 'background-color:' . $header . ';';
                    } elseif ($body && $striped === 'true' && $index % 2 === 1) {
                        $style .= 'background-color:#f9f9f9;';
                    }
                    self::prependStyle($cell, $style);
                }
            }
        }

        $out = '';
        foreach ($dom->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $out .= $dom->saveHTML($node);
        }
        return $out;
    }

    private static function prependStyle(\DOMElement $node, $style)
    {
        // Explicit cell styles still take precedence over wrapper defaults.
        if ($style !== '') $node->setAttribute('style', $style . $node->getAttribute('style'));
    }
}
