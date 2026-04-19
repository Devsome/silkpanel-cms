<?php

namespace App\Services;

class BBCodeService
{
    /**
     * @var array<int, string>
     */
    private array $allowedFonts = [
        'arial',
        'arial black',
        'book antiqua',
        'courier new',
        'georgia',
        'tahoma',
        'times new roman',
        'trebuchet ms',
        'verdana',
    ];

    public function toHtml(?string $bbcode): string
    {
        if (blank($bbcode)) {
            return '';
        }

        $content = str_replace(["\r\n", "\r"], "\n", trim($bbcode));
        $content = htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $placeholders = [];

        $content = $this->extractCodeBlocks($content, $placeholders);
        $content = $this->renderTagPairs($content);
        $content = $this->renderQuoteTags($content);
        $content = $this->renderColorTags($content);
        $content = $this->renderFontTags($content);
        $content = $this->renderSizeTags($content);
        $content = $this->renderEmailTags($content);
        $content = $this->renderLinks($content);
        $content = $this->renderMediaTags($content);
        $content = $this->renderImages($content);
        $content = $this->renderSpoilers($content);
        $content = $this->renderLists($content);
        $content = $this->renderTables($content);
        $content = $this->renderHorizontalRules($content);
        $content = $this->renderSimpleContainers($content);
        $content = nl2br($content);

        return strtr($content, $placeholders);
    }

    /**
     * @param  array<string, string>  $placeholders
     */
    private function extractCodeBlocks(string $content, array &$placeholders): string
    {
        return preg_replace_callback('/\[code\](.*?)\[\/code\]/is', function (array $matches) use (&$placeholders): string {
            $placeholder = '__BBCODE_CODE_' . count($placeholders) . '__';
            $placeholders[$placeholder] = '<pre><code>' . trim($matches[1]) . '</code></pre>';

            return $placeholder;
        }, $content) ?? $content;
    }

    private function renderTagPairs(string $content): string
    {
        $replacements = [
            'b' => ['<strong>', '</strong>'],
            'i' => ['<em>', '</em>'],
            'u' => ['<u>', '</u>'],
            's|strike' => ['<s>', '</s>'],
            'highlight' => ['<mark>', '</mark>'],
            'sub' => ['<sub>', '</sub>'],
            'sup' => ['<sup>', '</sup>'],
            'small' => ['<small>', '</small>'],
            'big' => ['<span class="text-lg">', '</span>'],
            'left' => ['<div class="text-left">', '</div>'],
            'center' => ['<div class="text-center">', '</div>'],
            'right' => ['<div class="text-right">', '</div>'],
            'indent' => ['<div class="ms-6">', '</div>'],
        ];

        foreach ($replacements as $bbcode => [$openingTag, $closingTag]) {
            $pattern = '/\[(?:' . $bbcode . ')\](.*?)\[\/(?:' . $bbcode . ')\]/is';

            do {
                $updated = preg_replace($pattern, $openingTag . '$1' . $closingTag, $content);

                if ($updated === null || $updated === $content) {
                    break;
                }

                $content = $updated;
            } while (true);
        }

        return $content;
    }

    private function renderQuoteTags(string $content): string
    {
        $content = preg_replace_callback('/\[quote=([^\]]+)\](.*?)\[\/quote\]/is', function (array $matches): string {
            $author = $this->cleanAttributeValue($matches[1]);

            if ($author === '') {
                return '<blockquote>' . $matches[2] . '</blockquote>';
            }

            return '<figure><figcaption>' . $this->escape($author) . ' schrieb:</figcaption><blockquote>' . $matches[2] . '</blockquote></figure>';
        }, $content) ?? $content;

        return preg_replace('/\[quote\](.*?)\[\/quote\]/is', '<blockquote>$1</blockquote>', $content) ?? $content;
    }

    private function renderColorTags(string $content): string
    {
        return preg_replace_callback('/\[color=([^\]]+)\](.*?)\[\/color\]/is', function (array $matches): string {
            $color = $this->cleanAttributeValue($matches[1]);

            if (!preg_match('/^(#[0-9a-fA-F]{3,8}|[a-zA-Z]{3,30})$/', $color)) {
                return $matches[2];
            }

            return '<span style="color: ' . $this->escape($color) . '">' . $matches[2] . '</span>';
        }, $content) ?? $content;
    }

    private function renderFontTags(string $content): string
    {
        return preg_replace_callback('/\[font=([^\]]+)\](.*?)\[\/font\]/is', function (array $matches): string {
            $font = $this->cleanAttributeValue($matches[1]);

            if (!in_array(strtolower($font), $this->allowedFonts, true)) {
                return $matches[2];
            }

            return '<span style="font-family: ' . $this->escape($font) . '">' . $matches[2] . '</span>';
        }, $content) ?? $content;
    }

    private function renderSizeTags(string $content): string
    {
        return preg_replace_callback('/\[size=([^\]]+)\](.*?)\[\/size\]/is', function (array $matches): string {
            $size = $this->cleanAttributeValue($matches[1]);
            $normalized = $this->normalizeFontSize($size);

            if ($normalized === null) {
                return $matches[2];
            }

            return '<span style="font-size: ' . $this->escape($normalized) . '">' . $matches[2] . '</span>';
        }, $content) ?? $content;
    }

    private function renderEmailTags(string $content): string
    {
        $content = preg_replace_callback('/\[email\](.*?)\[\/email\]/is', function (array $matches): string {
            $email = $this->sanitizeEmail($matches[1]);

            if ($email === null) {
                return $matches[1];
            }

            return '<a href="mailto:' . $this->escape($email) . '">' . $this->escape($email) . '</a>';
        }, $content) ?? $content;

        return preg_replace_callback('/\[email=([^\]]+)\](.*?)\[\/email\]/is', function (array $matches): string {
            $email = $this->sanitizeEmail($matches[1]);

            if ($email === null) {
                return $matches[2];
            }

            return '<a href="mailto:' . $this->escape($email) . '">' . $matches[2] . '</a>';
        }, $content) ?? $content;
    }

    private function renderLinks(string $content): string
    {
        $content = preg_replace_callback('/\[url\](.*?)\[\/url\]/is', function (array $matches): string {
            $url = $this->sanitizeUrl($matches[1]);

            if ($url === null) {
                return $matches[1];
            }

            return '<a href="' . $this->escape($url) . '" target="_blank" rel="noopener noreferrer">' . $matches[1] . '</a>';
        }, $content) ?? $content;

        return preg_replace_callback('/\[url=([^\]]+)\](.*?)\[\/url\]/is', function (array $matches): string {
            $url = $this->sanitizeUrl($matches[1]);

            if ($url === null) {
                return $matches[2];
            }

            return '<a href="' . $this->escape($url) . '" target="_blank" rel="noopener noreferrer">' . $matches[2] . '</a>';
        }, $content) ?? $content;
    }

    private function renderMediaTags(string $content): string
    {
        $content = preg_replace_callback('/\[(?:video|youtube)\](.*?)\[\/(?:video|youtube)\]/is', function (array $matches): string {
            $url = $this->sanitizeUrl($matches[1]);

            if ($url === null) {
                return $matches[1];
            }

            return '<p><a href="' . $this->escape($url) . '" target="_blank" rel="noopener noreferrer">Video ansehen</a></p>';
        }, $content) ?? $content;

        return preg_replace_callback('/\[(?:video|youtube)=([^\]]+)\](.*?)\[\/(?:video|youtube)\]/is', function (array $matches): string {
            $url = $this->sanitizeUrl($matches[1]) ?? $this->sanitizeUrl($matches[2]);

            if ($url === null) {
                return $matches[2];
            }

            $label = trim($matches[2]) !== '' ? $matches[2] : 'Video ansehen';

            return '<p><a href="' . $this->escape($url) . '" target="_blank" rel="noopener noreferrer">' . $label . '</a></p>';
        }, $content) ?? $content;
    }

    private function renderImages(string $content): string
    {
        $content = preg_replace_callback('/\[img\](.*?)\[\/img\]/is', function (array $matches): string {
            $url = $this->sanitizeUrl($matches[1]);

            if ($url === null) {
                return $matches[1];
            }

            return $this->buildImageTag($url);
        }, $content) ?? $content;

        return preg_replace_callback('/\[img=(\d+)x(\d+)\](.*?)\[\/img\]/is', function (array $matches): string {
            $url = $this->sanitizeUrl($matches[3]);

            if ($url === null) {
                return $matches[3];
            }

            return $this->buildImageTag($url, min((int) $matches[1], 2000), min((int) $matches[2], 2000));
        }, $content) ?? $content;
    }

    private function renderSpoilers(string $content): string
    {
        return preg_replace_callback('/\[spoiler(?:=([^\]]+))?\](.*?)\[\/spoiler\]/is', function (array $matches): string {
            $label = isset($matches[1]) ? $this->cleanAttributeValue($matches[1]) : '';
            $label = $label !== '' ? $label : 'Spoiler';

            return '<details><summary>' . $this->escape($label) . '</summary>' . $matches[2] . '</details>';
        }, $content) ?? $content;
    }

    private function renderLists(string $content): string
    {
        while (preg_match('/\[list(?:=([^\]]+))?\](.*?)\[\/list\]/is', $content)) {
            $content = preg_replace_callback('/\[list(?:=([^\]]+))?\](.*?)\[\/list\]/is', function (array $matches): string {
                $type = ($matches[1] ?? '') !== '' ? $this->cleanAttributeValue($matches[1]) : null;
                $items = preg_split('/\[\*\]/i', $matches[2]) ?: [];
                $items = array_values(array_filter(array_map(static fn(string $item): string => trim($item), $items)));

                if ($items === []) {
                    return $matches[2];
                }

                $tag = $type === null ? 'ul' : 'ol';
                $allowedTypes = ['1', 'a', 'A', 'i', 'I'];
                $typeAttribute = $tag === 'ol' && in_array($type, $allowedTypes, true)
                    ? ' type="' . $this->escape($type) . '"'
                    : '';

                $html = '<' . $tag . $typeAttribute . '>';

                foreach ($items as $item) {
                    $html .= '<li>' . $item . '</li>';
                }

                $html .= '</' . $tag . '>';

                return $html;
            }, $content) ?? $content;
        }

        return $content;
    }

    private function renderTables(string $content): string
    {
        while (preg_match('/\[table(?:=([^\]]+))?\](.*?)\[\/table\]/is', $content)) {
            $content = preg_replace_callback('/\[table(?:=([^\]]+))?\](.*?)\[\/table\]/is', function (array $matches): string {
                $option = ($matches[1] ?? '') !== '' ? strtolower($this->cleanAttributeValue($matches[1])) : null;
                $inner = trim($matches[2]);

                if (str_contains($inner, '|')) {
                    return $this->renderPipeTable($inner, $option === 'head');
                }

                return '<table>' . $inner . '</table>';
            }, $content) ?? $content;
        }

        $patterns = [
            '/\[tr\](.*?)\[\/tr\]/is' => '<tr>$1</tr>',
            '/\[th\](.*?)\[\/th\]/is' => '<th>$1</th>',
            '/\[td\](.*?)\[\/td\]/is' => '<td>$1</td>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            do {
                $updated = preg_replace($pattern, $replacement, $content);

                if ($updated === null || $updated === $content) {
                    break;
                }

                $content = $updated;
            } while (true);
        }

        return $content;
    }

    private function renderHorizontalRules(string $content): string
    {
        $content = preg_replace('/\[hr\]/i', '<hr>', $content) ?? $content;

        return preg_replace_callback('/\[hr(?:=([^\]]+))?\](.*?)\[\/hr\]/is', function (array $matches): string {
            $variant = ($matches[1] ?? '') !== '' ? $this->cleanAttributeValue($matches[1]) : null;
            $width = trim(html_entity_decode($matches[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            $style = [];

            if (preg_match('/^(\d{1,3})%$/', $width, $widthMatches) === 1) {
                $numericWidth = min(max((int) $widthMatches[1], 1), 100);
                $style[] = 'width: ' . $numericWidth . '%';
            }

            if ($variant !== null) {
                $style[] = match ($variant) {
                    '1' => 'border-top-style: dashed',
                    '2' => 'border-top-style: dotted',
                    default => 'border-top-style: solid',
                };
            }

            $attribute = $style !== []
                ? ' style="' . $this->escape(implode('; ', $style)) . '"'
                : '';

            return '<hr' . $attribute . '>';
        }, $content) ?? $content;
    }

    private function renderSimpleContainers(string $content): string
    {
        return preg_replace_callback('/\[(left|center|right)\](.*?)\[\/\1\]/is', function (array $matches): string {
            $alignment = strtolower($matches[1]);

            return '<div class="text-' . $alignment . '">' . $matches[2] . '</div>';
        }, $content) ?? $content;
    }

    private function renderPipeTable(string $inner, bool $withHead): string
    {
        $rows = preg_split('/\n+/', trim($inner)) ?: [];
        $rows = array_values(array_filter(array_map(static fn(string $row): string => trim($row), $rows)));

        if ($rows === []) {
            return $inner;
        }

        $table = '<table>';

        if ($withHead) {
            $headCells = $this->splitTableRow(array_shift($rows));
            $table .= '<thead><tr>';

            foreach ($headCells as $cell) {
                $table .= '<th>' . $cell . '</th>';
            }

            $table .= '</tr></thead>';
        }

        $table .= '<tbody>';

        foreach ($rows as $row) {
            $table .= '<tr>';

            foreach ($this->splitTableRow($row) as $cell) {
                $table .= '<td>' . $cell . '</td>';
            }

            $table .= '</tr>';
        }

        $table .= '</tbody></table>';

        return $table;
    }

    /**
     * @return array<int, string>
     */
    private function splitTableRow(string $row): array
    {
        return array_map(
            static fn(string $cell): string => trim($cell),
            explode('|', $row),
        );
    }

    private function buildImageTag(string $url, ?int $width = null, ?int $height = null): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $alt = basename($path) ?: 'Image';
        $sizeAttributes = '';

        if ($width !== null) {
            $sizeAttributes .= ' width="' . $width . '"';
        }

        if ($height !== null) {
            $sizeAttributes .= ' height="' . $height . '"';
        }

        return '<img src="' . $this->escape($url) . '" alt="' . $this->escape($alt) . '" loading="lazy" class="rounded-lg"' . $sizeAttributes . '>';
    }

    private function normalizeFontSize(string $size): ?string
    {
        $namedSizes = [
            '1' => '0.75rem',
            '2' => '0.875rem',
            '3' => '1rem',
            '4' => '1.125rem',
            '5' => '1.25rem',
            '6' => '1.5rem',
            '7' => '1.875rem',
        ];

        if (isset($namedSizes[$size])) {
            return $namedSizes[$size];
        }

        if (preg_match('/^(\d{1,3})(px|em|rem|%)$/i', $size, $matches) !== 1) {
            return null;
        }

        $value = (int) $matches[1];
        $unit = strtolower($matches[2]);

        if ($unit === '%' && ($value < 50 || $value > 300)) {
            return null;
        }

        if (in_array($unit, ['px', 'em', 'rem'], true) && ($value < 8 || $value > 72)) {
            return null;
        }

        return $value . $unit;
    }

    private function sanitizeEmail(string $email): ?string
    {
        $email = html_entity_decode(trim($email), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if ($email === '') {
            return null;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) ?: null;
    }

    private function sanitizeUrl(string $url): ?string
    {
        $url = $this->cleanAttributeValue($url);

        if ($url === '') {
            return null;
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if (!in_array($scheme, ['http', 'https', 'mailto'], true)) {
            return null;
        }

        return $url;
    }

    private function cleanAttributeValue(string $value): string
    {
        $value = html_entity_decode(trim($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        return trim($value);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
