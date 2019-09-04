/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/'use strict';

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

(function (wp) {
  var blocks = wp.blocks,
      data = wp.data,
      element = wp.element,
      components = wp.components,
      editor = wp.editor;
  var registerBlockType = blocks.registerBlockType;
  var dispatch = data.dispatch,
      select = data.select;
  var Fragment = element.Fragment;
  var PanelBody = components.PanelBody,
      BaseControl = components.BaseControl,
      Icon = components.Icon,
      RangeControl = components.RangeControl,
      IconButton = components.IconButton,
      Toolbar = components.Toolbar,
      SelectControl = components.SelectControl;
  var InnerBlocks = editor.InnerBlocks,
      RichText = editor.RichText,
      InspectorControls = editor.InspectorControls,
      PanelColorSettings = editor.PanelColorSettings,
      MediaUpload = editor.MediaUpload,
      BlockControls = editor.BlockControls;

  var __ = Drupal.t;

  var TEMPLATE = [['core/image', {
    align: 'center'
  }], ['core/heading', {
    placeholder: 'Card title...',
    content: 'The StarterX Theme',
    level: 3
  }], ['core/paragraph', {
    placeholder: 'Card description'
  }]];

  var settings = {
    title: __('Card'),
    description: __('Card'),
    icon: 'media-spreadsheet',
    attributes: {},
    parent: ['starterx/cards-block'],

    edit: function edit(_ref) {
      var className = _ref.className,
          attributes = _ref.attributes,
          setAttributes = _ref.setAttributes,
          isSelected = _ref.isSelected;

      return React.createElement(
        Fragment,
        null,
        React.createElement(
          'div',
          { className: className },
          React.createElement(InnerBlocks, { template: TEMPLATE, templateLock: 'all' })
        )
      );
    },
    save: function save(_ref2) {
      var className = _ref2.className,
          attributes = _ref2.attributes;


      return React.createElement(
        'div',
        { className: className },
        React.createElement(InnerBlocks.Content, null)
      );
    }
  };

  var category = {
    slug: 'starterx',
    title: __('StarterX Blocks')
  };

  var currentCategories = select('core/blocks').getCategories().filter(function (item) {
    return item.slug !== category.slug;
  });
  dispatch('core/blocks').setCategories([category].concat(_toConsumableArray(currentCategories)));

  registerBlockType(category.slug + '/card', _extends({ category: category.slug }, settings));
})(wp);