(function (wp) {
  const {blocks, data, element, components, editor} = wp;
  const {registerBlockType} = blocks;
  const {dispatch, select} = data;
  const {Fragment} = element;
  const {PanelBody, BaseControl, Icon, RangeControl, IconButton, Toolbar, SelectControl} = components;
  const {InnerBlocks, RichText, InspectorControls, PanelColorSettings, MediaUpload, BlockControls} = editor;
  const __ = Drupal.t;

  const TEMPLATE = [
    ['core/image', {
    align: 'center',
    }],
    ['core/heading', {
      placeholder: 'Card title...',
      content: 'The StarterX Theme',
      level: 3,
    }],
    ['core/paragraph', {
      placeholder: 'Card description',
    }],
  ];

  const settings = {
    title: __('Card'),
    description: __('Card'),
    icon: 'media-spreadsheet',
    attributes: {},
    parent: ['starterx/cards-block'],

    edit({className, attributes, setAttributes, isSelected}) {
      return (
        <Fragment>
          <div className={className}>
            <InnerBlocks template={TEMPLATE} templateLock={'all'}/>
          </div>
        </Fragment>
      )
    },
    save({className, attributes}) {

      return (
        <div className={className}>
            <InnerBlocks.Content/>
        </div>
      );
    }
    ,
  };

  const category = {
    slug: 'starterx',
    title: __('StarterX Blocks')
  };

  const currentCategories = select('core/blocks').getCategories().filter(item => item.slug !== category.slug);
  dispatch('core/blocks').setCategories([category, ...currentCategories]);

  registerBlockType(`${category.slug}/card`, {category: category.slug, ...settings});
})
(wp);
