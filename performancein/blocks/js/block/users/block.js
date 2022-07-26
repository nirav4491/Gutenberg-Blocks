import './editor.css';
import './front.css';
import {GridIcon} from '../icons';

(function(wpI18n, wpBlocks, wpElement, wpEditor, wpComponents) {
  const {__} = wpI18n;
  const {registerBlockType} = wpBlocks;
  const {Component,Fragment} = wpElement;
  const {InspectorControls} = wpEditor;
  const {ServerSideRender, RangeControl, PanelBody} = wpComponents;

  class PerformanceINUsers extends Component {
    constructor() {
      super(...arguments);

    }
    render() {
      const { attributes: { userPerPage }, className, setAttributes, isSelected } = this.props;
      return (
        <Fragment>
          <InspectorControls>
            <PanelBody title={ __('User Filter Settings') } initialOpen={ false }>
              <RangeControl
                label={ __( 'No of User to show per page' ) }
                value={ userPerPage }
                onChange={ ( value ) => setAttributes( { userPerPage: value } ) }
                min={ 0 }
              />
            </PanelBody>
          </InspectorControls>
          <ServerSideRender
            block="performancein/user-grid"
            attributes={ {
              userPerPage: userPerPage,
            } }
          />
        </Fragment>
      );
    }

  }


  registerBlockType('performancein/user-grid', {
    title: __('User Filter'),
    icon: GridIcon,
    category: 'performancein',
    keywords: [__('Post'), __('Grid')],
    description: __( 'It showcase author listing ' ),
    example: {
      attributes: {
        caption: __( 'Author Listing' ),
      },
    },
    attributes:{
      userPerPage: {
        type: 'number',
        default: 0
      }
    },
    edit: PerformanceINUsers,
    save() {
      return null;
    },
  });

})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.components);
