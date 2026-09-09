<?php

namespace App\Services;

use ZipArchive;

class DocxBuilder
{
    private array $bodyXml = [];

    private const FONT = 'Times New Roman';
    private const COLOR_PRIMARY = '1F4E79';
    private const COLOR_HEADER_BG = 'D6E4F0';
    private const COLOR_FOOTER_BG = 'F2F2F2';

    public function addPageBreak(): self
    {
        $this->bodyXml[] = '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';
        return $this;
    }

    public function addTextBreak(int $count = 1): self
    {
        for ($i = 0; $i < $count; $i++) {
            $this->bodyXml[] = '<w:p><w:pPr><w:spacing w:after="0"/></w:pPr></w:p>';
        }
        return $this;
    }

    public function addText(string $text, array $fontOpts = [], array $paraOpts = []): self
    {
        $this->bodyXml[] = $this->buildParagraph($text, $fontOpts, $paraOpts);
        return $this;
    }

    public function addTitle(string $text, int $level = 1): self
    {
        $size = $level === 1 ? 32 : 26;
        $spaceAfter = $level === 1 ? 120 : 80;
        $spaceBefore = $level === 1 ? 0 : 240;

        $rPr = '<w:rPr>'
            . '<w:rFonts w:ascii="' . self::FONT . '" w:hAnsi="' . self::FONT . '"/>'
            . '<w:b/><w:bCs/>'
            . '<w:color w:val="' . self::COLOR_PRIMARY . '"/>'
            . '<w:sz w:val="' . $size . '"/><w:szCs w:val="' . $size . '"/>'
            . '</w:rPr>';

        $pPr = '<w:pPr>'
            . '<w:pStyle w:val="Heading' . $level . '"/>'
            . '<w:spacing w:before="' . $spaceBefore . '" w:after="' . $spaceAfter . '"/>'
            . '</w:pPr>';

        $this->bodyXml[] = '<w:p>' . $pPr . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $this->esc($text) . '</w:t></w:r></w:p>';
        return $this;
    }

    public function addListItem(string $text, array $fontOpts = []): self
    {
        $sz = ($fontOpts['size'] ?? 12) * 2;
        $rPr = '<w:rPr>'
            . '<w:rFonts w:ascii="' . self::FONT . '" w:hAnsi="' . self::FONT . '"/>'
            . '<w:sz w:val="' . $sz . '"/><w:szCs w:val="' . $sz . '"/>'
            . '</w:rPr>';

        $pPr = '<w:pPr>'
            . '<w:pStyle w:val="ListParagraph"/>'
            . '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr>'
            . '</w:pPr>';

        $this->bodyXml[] = '<w:p>' . $pPr . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $this->esc($text) . '</w:t></w:r></w:p>';
        return $this;
    }

    public function addTable(array $rows, array $colWidths = []): self
    {
        $totalWidth = array_sum($colWidths) ?: 9000;
        $xml = '<w:tbl>';
        $xml .= '<w:tblPr>'
            . '<w:tblStyle w:val="TableGrid"/>'
            . '<w:tblW w:w="' . $totalWidth . '" w:type="dxa"/>'
            . '<w:tblBorders>'
            . '<w:top w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>'
            . '<w:left w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>'
            . '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>'
            . '<w:right w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>'
            . '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>'
            . '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="CCCCCC"/>'
            . '</w:tblBorders>'
            . '<w:tblCellMar><w:top w:w="40" w:type="dxa"/><w:left w:w="80" w:type="dxa"/><w:bottom w:w="40" w:type="dxa"/><w:right w:w="80" w:type="dxa"/></w:tblCellMar>'
            . '</w:tblPr>';

        if ($colWidths) {
            $xml .= '<w:tblGrid>';
            foreach ($colWidths as $w) {
                $xml .= '<w:gridCol w:w="' . $w . '"/>';
            }
            $xml .= '</w:tblGrid>';
        }

        foreach ($rows as $row) {
            $xml .= '<w:tr>';
            foreach ($row['cells'] as $ci => $cell) {
                $cellW = $colWidths[$ci] ?? 3000;
                $bgColor = $cell['bg'] ?? null;
                $tcPr = '<w:tcPr><w:tcW w:w="' . $cellW . '" w:type="dxa"/>';
                if ($bgColor) {
                    $tcPr .= '<w:shd w:val="clear" w:color="auto" w:fill="' . $bgColor . '"/>';
                }
                $tcPr .= '</w:tcPr>';

                $align = $cell['align'] ?? 'left';
                $jc = match ($align) {
                    'center' => 'center',
                    'right', 'end' => 'end',
                    default => 'start',
                };

                $sz = ($cell['size'] ?? 11) * 2;
                $rPr = '<w:rPr>'
                    . '<w:rFonts w:ascii="' . self::FONT . '" w:hAnsi="' . self::FONT . '"/>'
                    . '<w:sz w:val="' . $sz . '"/><w:szCs w:val="' . $sz . '"/>';
                if (!empty($cell['bold'])) {
                    $rPr .= '<w:b/><w:bCs/>';
                }
                if (!empty($cell['color'])) {
                    $rPr .= '<w:color w:val="' . $cell['color'] . '"/>';
                }
                $rPr .= '</w:rPr>';

                $xml .= '<w:tc>' . $tcPr
                    . '<w:p><w:pPr><w:jc w:val="' . $jc . '"/></w:pPr>'
                    . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $this->esc($cell['text'] ?? '') . '</w:t></w:r>'
                    . '</w:p></w:tc>';
            }
            $xml .= '</w:tr>';
        }

        $xml .= '</w:tbl>';
        $this->bodyXml[] = $xml;
        return $this;
    }

    public function save(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($path)) {
            unlink($path);
        }

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create docx at: $path");
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rels());
        $zip->addFromString('word/_rels/document.xml.rels', $this->documentRels());
        $zip->addFromString('word/document.xml', $this->documentXml());
        $zip->addFromString('word/styles.xml', $this->stylesXml());
        $zip->addFromString('word/numbering.xml', $this->numberingXml());
        $zip->addFromString('word/settings.xml', $this->settingsXml());

        $zip->close();
    }

    private function esc(string $text): string
    {
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function buildParagraph(string $text, array $fontOpts, array $paraOpts): string
    {
        $sz = ($fontOpts['size'] ?? 12) * 2;

        $rPr = '<w:rPr>'
            . '<w:rFonts w:ascii="' . self::FONT . '" w:hAnsi="' . self::FONT . '"/>'
            . '<w:sz w:val="' . $sz . '"/><w:szCs w:val="' . $sz . '"/>';
        if (!empty($fontOpts['bold'])) {
            $rPr .= '<w:b/><w:bCs/>';
        }
        if (!empty($fontOpts['italic'])) {
            $rPr .= '<w:i/><w:iCs/>';
        }
        if (!empty($fontOpts['color'])) {
            $rPr .= '<w:color w:val="' . $fontOpts['color'] . '"/>';
        }
        $rPr .= '</w:rPr>';

        $align = $paraOpts['alignment'] ?? null;
        $jc = match ($align) {
            'center' => 'center',
            'right', 'end' => 'end',
            'both' => 'both',
            default => null,
        };

        $spaceAfter = $paraOpts['spaceAfter'] ?? 200;

        $pPr = '<w:pPr>';
        if ($jc) {
            $pPr .= '<w:jc w:val="' . $jc . '"/>';
        }
        $pPr .= '<w:spacing w:after="' . $spaceAfter . '"/>';
        $pPr .= '</w:pPr>';

        return '<w:p>' . $pPr . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $this->esc($text) . '</w:t></w:r></w:p>';
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>'
            . '<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>'
            . '<Override PartName="/word/numbering.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.numbering+xml"/>'
            . '<Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>'
            . '</Types>';
    }

    private function rels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>'
            . '</Relationships>';
    }

    private function documentRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/numbering" Target="numbering.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>'
            . '</Relationships>';
    }

    private function documentXml(): string
    {
        $body = implode('', $this->bodyXml);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas"'
            . ' xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
            . ' xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"'
            . ' xmlns:v="urn:schemas-microsoft-com:vml"'
            . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"'
            . ' xmlns:w10="urn:schemas-microsoft-com:office:word"'
            . ' xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
            . ' xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml"'
            . ' xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup"'
            . ' xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk"'
            . ' xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml"'
            . ' xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape"'
            . ' mc:Ignorable="w14 wp14">'
            . '<w:body>'
            . $body
            . '<w:sectPr>'
            . '<w:pgSz w:w="12240" w:h="15840"/>'
            . '<w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>'
            . '</w:sectPr>'
            . '</w:body>'
            . '</w:document>';
    }

    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<w:docDefaults>'
            . '<w:rPrDefault><w:rPr>'
            . '<w:rFonts w:ascii="' . self::FONT . '" w:eastAsia="' . self::FONT . '" w:hAnsi="' . self::FONT . '" w:cs="' . self::FONT . '"/>'
            . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
            . '</w:rPr></w:rPrDefault>'
            . '<w:pPrDefault><w:pPr><w:spacing w:after="200" w:line="276" w:lineRule="auto"/></w:pPr></w:pPrDefault>'
            . '</w:docDefaults>'
            . '<w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></w:style>'
            . '<w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/>'
            . '<w:pPr><w:keepNext/><w:spacing w:before="0" w:after="120"/></w:pPr>'
            . '<w:rPr><w:b/><w:bCs/><w:color w:val="' . self::COLOR_PRIMARY . '"/><w:sz w:val="32"/><w:szCs w:val="32"/></w:rPr>'
            . '</w:style>'
            . '<w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/>'
            . '<w:pPr><w:keepNext/><w:spacing w:before="240" w:after="80"/></w:pPr>'
            . '<w:rPr><w:b/><w:bCs/><w:color w:val="' . self::COLOR_PRIMARY . '"/><w:sz w:val="26"/><w:szCs w:val="26"/></w:rPr>'
            . '</w:style>'
            . '<w:style w:type="paragraph" w:styleId="ListParagraph"><w:name w:val="List Paragraph"/><w:basedOn w:val="Normal"/>'
            . '<w:pPr><w:ind w:left="720"/></w:pPr>'
            . '</w:style>'
            . '<w:style w:type="table" w:default="1" w:styleId="TableNormal"><w:name w:val="Normal Table"/>'
            . '<w:tblPr><w:tblCellMar><w:top w:w="0" w:type="dxa"/><w:left w:w="108" w:type="dxa"/><w:bottom w:w="0" w:type="dxa"/><w:right w:w="108" w:type="dxa"/></w:tblCellMar></w:tblPr>'
            . '</w:style>'
            . '<w:style w:type="table" w:styleId="TableGrid"><w:name w:val="Table Grid"/><w:basedOn w:val="TableNormal"/>'
            . '<w:tblPr><w:tblBorders>'
            . '<w:top w:val="single" w:sz="4" w:space="0" w:color="auto"/>'
            . '<w:left w:val="single" w:sz="4" w:space="0" w:color="auto"/>'
            . '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="auto"/>'
            . '<w:right w:val="single" w:sz="4" w:space="0" w:color="auto"/>'
            . '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="auto"/>'
            . '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="auto"/>'
            . '</w:tblBorders></w:tblPr>'
            . '</w:style>'
            . '</w:styles>';
    }

    private function numberingXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:abstractNum w:abstractNumId="0">'
            . '<w:lvl w:ilvl="0">'
            . '<w:start w:val="1"/>'
            . '<w:numFmt w:val="bullet"/>'
            . '<w:lvlText w:val="&#x2022;"/>'
            . '<w:lvlJc w:val="left"/>'
            . '<w:pPr><w:ind w:left="720" w:hanging="360"/></w:pPr>'
            . '<w:rPr><w:rFonts w:ascii="Symbol" w:hAnsi="Symbol" w:hint="default"/></w:rPr>'
            . '</w:lvl>'
            . '</w:abstractNum>'
            . '<w:num w:numId="1"><w:abstractNumId w:val="0"/></w:num>'
            . '</w:numbering>';
    }

    private function settingsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
            . ' xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"'
            . ' xmlns:v="urn:schemas-microsoft-com:vml"'
            . ' xmlns:w10="urn:schemas-microsoft-com:office:word">'
            . '<w:zoom w:percent="100"/>'
            . '<w:defaultTabStop w:val="720"/>'
            . '<w:characterSpacingControl w:val="doNotCompress"/>'
            . '<w:compat><w:compatSetting w:name="compatibilityMode" w:uri="http://schemas.microsoft.com/office/word" w:val="15"/></w:compat>'
            . '</w:settings>';
    }
}
