<?php

/**
 * @file
 * Lists available colors and color schemes for the Starter X Drupal theme.
 */

$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'base' => t('Call to action'),
    'link' => t('Text links / call to action hover'),
    'headings' => t('Headings'),
    'text' => t('Paragraphs'),
    'label-label-text-background' => t('Label / label text background'),
    'input-text' => t('Input text'),
    'borders' => t('Borders'),
    'backgrounds' => t('Backgrounds'),
  ],

  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('Starter X Default'),
      'colors' => [
        'base' => '#e93350',
        'link' => '#b91e37',
        'headings' => '#202124',
        'text' => '#2c3238',
        'label-label-text-background' => '#4f5c65',
        'input-text' => '#8c959b',
        'borders' => '#d1dbe5',
        'backgrounds' => '#f3f6f9',
      ],
    ],
  ],

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => [
    'build/css/main.css',
  ],

  // Files to copy.
  'copy' => [
    'logo.svg',
  ],

  // Gradient definitions.
  'gradients' => [[]],

  // Preview files.
  'preview_library' => 'starterx/color.preview',
  'preview_html' => 'color/preview.html',

  // Attachments.
  '#attached' => [
    'drupalSettings' => [
      'color' => [
        // Put the logo path into JavaScript for the live preview.
        'logo' => theme_get_setting('logo.url', 'starterx'),
      ],
    ],
  ],
];

$info['css'] = ['build/css/main.css'];