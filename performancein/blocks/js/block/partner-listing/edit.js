const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;
const {Fragment, RawHTML, Component} = wp.element;
const {MediaUpload, AlignmentToolbar, InspectorControls, InnerBlocks, PanelColorSettings, BlockAlignmentToolbar, RichText} = wp.blockEditor;
const {PanelBody, TextControl, Button, SelectControl, RangeControl, ToggleControl, ServerSideRender, RadioControl, Icon, QueryControls} = wp.components;

const { addQueryArgs } = wp.url;

class Edit extends Component {

  constructor() {
    super();
    this.state = {
      categoriesList: [],
    };
  }

  componentDidMount() {

    const {attributes, setAttributes, clientId} = this.props;
    let postTypeKey;
    let optionsArr = [{label: __('Select the post'), value: 'all'}];
    wp.apiFetch({path: "performancein_api/request/v1/partner/listing/"}).then(categories => {
      postTypeKey = Object.keys(categories);
      postTypeKey.forEach(function (key) {
        optionsArr.push({
          label: __(categories[key].name),
          value: __(categories[key].slug)
        });
      });
      optionsArr.push({
        label: __('All Tags'),
        value: __('partnerNetworkTag')
      });
      setAttributes( { categories: optionsArr } );
    });
  }

  render () {
    const { attributes: { categories, categories_value, postsToShow }, className, setAttributes, isSelected } = this.props;
    const { categoriesList } = this.state;

    return(
      <Fragment>
        <InspectorControls>
          <div className="partner-listing-posts-admin">
            <PanelBody title={ __( 'Sorting and Filtering' ) } initialOpen={ false }>
              <SelectControl
                label="Category"
                value={ categories_value }
                onChange={ (value) => setAttributes( { categories_value: value } ) }
                options={ categories }
              />
              { categories_value === 'basic-membership' &&
              <RangeControl
                label="Number of items"
                value={ postsToShow }
                onChange={ (value) => setAttributes({postsToShow: value}) }
                min={ 1 }
                max={ 20 }
              />
              }
            </PanelBody>
          </div>
        </InspectorControls>
        <ServerSideRender
          block="performancein/partner-listing"
          attributes={ {
            categories_value: categories_value,
            postsToShow: postsToShow,
          } }
        />
      </Fragment>
    );
  }
}

export default Edit;
