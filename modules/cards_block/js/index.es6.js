(function (wp) {
  const {blocks, data, element, components, editor} = wp;
  const {registerBlockType} = blocks;
  const {dispatch, select} = data;
  const {Fragment} = element;
  const {PanelBody, BaseControl, Icon, RangeControl, IconButton, Toolbar, SelectControl} = components;
  const {InnerBlocks, RichText, InspectorControls, PanelColorSettings, MediaUpload, BlockControls} = editor;
  const __ = Drupal.t;

  const TEMPLATES = ['starterx/card'];

  const settings = {
    title: __('Gutenberg Cards Block'),
    description: __('Gutenberg Cards Block'),
    icon: 'images-alt2',
    attributes: {
      align: {
        type: 'string',
        default: 'full'
      }
    },
    edit({className}) {
      return (
        <Fragment>
          <div className={className}>
              <InnerBlocks template={[TEMPLATES]} templateLock={false} allowedBlocks={TEMPLATES}/>
          </div>
        </Fragment>
      )
    },
    save({className}) {

      return (
        <div className={className}>
            <InnerBlocks.Content/>
        </div>
      );
    },
    getEditWrapperProps (attributes) {
      const {align} = attributes;
      return {'data-align': align};
    },
  };

  const category = {
    slug: 'starterx',
    title: __('StarterX Blocks')
  };

  const currentCategories = select('core/blocks').getCategories().filter(item => item.slug !== category.slug);
  dispatch('core/blocks').setCategories([category, ...currentCategories]);

  registerBlockType(`${category.slug}/cards-block`, {category: category.slug, ...settings});
})
(wp);
