<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<peep@inane.co.za>
 * @package Inane\Stdlib
 *
 * @version    GIT: $Id$
 * @date       $Date$
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 */

declare(strict_types=1);

namespace Inane\Stdlib;

use function in_array;
use function ini_get;
use function ini_set;
use const null;

/**
 * Highlight
 *
 * PHP highlight values
 *
 * @package Inane\Stdlib
 *
 * @version 0.3.0
 */
enum Highlight {
    /**
     * Use current values
     */
    case CURRENT;
    /**
     * The colours straight out the box.
     */
    case DEFAULT;
    /**
     * Somebody's idea of what the default should be.
     */
    case PHP2;
    /**
     * An html styled colour highlight.
     */
    case HTML;

    /**
     * Highlight property values
     *
     * @param null|string $setting options: comment, default, html, keyword, string or null for all
     *
     * @return array|string setting or settings array
     */
    public function settings(?string $setting = null): array|string {
        $settings = match ($this) {
            static::CURRENT => [
                'highlight.comment' => ini_get('highlight.comment'),
                'highlight.default' => ini_get('highlight.default'),
                'highlight.html'    => ini_get('highlight.html'),
                'highlight.keyword' => ini_get('highlight.keyword'),
                'highlight.string'  => ini_get('highlight.string')
            ],
            static::PHP2 => [
                'highlight.comment' => '#008000',
                'highlight.default' => '#000000',
                'highlight.html'    => '#808080',
                'highlight.keyword' => '#0000BB; font-weight: bold',
                'highlight.string'  => '#DD0000'
            ],
            static::HTML => [
                'highlight.comment' => '#008000',
                'highlight.default' => '#CC0000',
                'highlight.html'    => '#000000',
                'highlight.keyword' => '#000000; font-weight: bold',
                'highlight.string'  => '#0000FF'
            ],
            default => [
                'highlight.comment' => '#FF8000',
                'highlight.default' => '#0000BB',
                'highlight.html'    => '#000000',
                'highlight.keyword' => '#007700',
                'highlight.string'  => '#DD0000'
            ],
        };

        if (in_array($setting, [
            'comment',
            'default',
            'html',
            'keyword',
            'string',
        ])) return $settings["highlight.{$setting}"];
        return $settings;
    }

    /**
     * Apply $this highlight
     *
     * @return void
     */
    public function apply(): void {
        static::applyHighlight($this);
    }

    /**
     * Apply Highlight $highlight
     *
     * @param \Inane\Stdlib\Highlight $highlight
     *
     * @return void
     */
    public static function applyHighlight(Highlight $highlight): void {
        foreach ($highlight->settings() as $key => $val) ini_set($key, $val);
    }

    /**
     * Get highlight comment value
     *
     * @return string comment
     */
    public function commentHighlight(): string {
        return $this->settings('comment');
    }

    /**
     * Get highlight default value
     *
     * @return string default
     */
    public function defaultHighlight(): string {
        return $this->settings('default');
    }

    /**
     * Get highlight html value
     *
     * @return string html
     */
    public function htmlHighlight(): string {
        return $this->settings('html');
    }

    /**
     * Get highlight keyword value
     *
     * @return string keyword
     */
    public function keywordHighlight(): string {
        return $this->settings('keyword');
    }

    /**
     * Get highlight string value
     *
     * @return string string
     */
    public function stringHighlight(): string {
        return $this->settings('string');
    }
}
