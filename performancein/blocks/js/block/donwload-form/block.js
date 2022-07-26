import './editor.css';
import './front.css';
import {RequestFormIcon} from '../icons';

(function(wpI18n, wpBlocks, wpElement, wpEditor, wpComponents) {
  const {__} = wpI18n;
  const {Component, Fragment} = wpElement;
  const {registerBlockType} = wpBlocks;
  const {InspectorControls} = wpEditor;
  const {
    TextControl,
    PanelBody,
    PanelRow,
    ServerSideRender,
    ToggleControl,
    SelectControl,
  } = wpComponents;

  class PerformanceinDownloadForm extends Component {
    constructor() {
      super();
      this.state = {
        ContactFormLists: [],
      };
    }

    componentDidMount() {

      const {attributes, setAttributes} = this.props;
      let postTypeKey;
      let optionsArr = [{label: __('Select Contact Form'), value: ''}];
      wp.apiFetch({path: 'contact_form_api/request/v1/contactform/listing/'}).then(contactforms => {
        postTypeKey = Object.keys(contactforms);
        postTypeKey.forEach(function(key) {
          optionsArr.push({
            label: __(contactforms[key].name),
            value: __(contactforms[key].id),
          });
        });
        setAttributes({contactforms: optionsArr});
      });
    }

    render() {
      const {
        attributes: {header, footertext, contactforms, contactform_value},
        setAttributes,
      } = this.props;
      const {ContactFormLists} = this.state;
      return (
        <Fragment>
          <InspectorControls>
            <PanelBody
              title={ __('Heading Settings') }
              className="heading-setting"
            >
              <PanelRow>
                <TextControl
                  type="string"
                  label="Header"
                  name={ header }
                  value={ header }
                  placeHolder="Form Header"
                  onChange={ value => setAttributes({header: value}) }
                />
              </PanelRow>
              <PanelRow>
                <SelectControl
                  label="Contact Forms"
                  value={ contactform_value }
                  onChange={ (value) => setAttributes({contactform_value: value}) }
                  options={ contactforms }
                />
              </PanelRow>
              <PanelRow>
                <TextControl
                  type="string"
                  label="Footer Text"
                  name={ footertext }
                  value={ footertext }
                  placeHolder="Form Sub Header"
                  onChange={ value => setAttributes({footertext: value}) }
                />
              </PanelRow>
            </PanelBody>
          </InspectorControls>
          <ServerSideRender
            block="performancein/download-form"
            attributes={ {
              header: header,
              footertext: footertext,
              contactform_value: contactform_value,
            } }
          />
        </Fragment>
      );
    }
  }

  const allAttr = {
    header: {
      type: 'string',
      default: 'Download Now',
    },
    footertext: {
      type: 'string',
      default: 'By downloading the media pack you are consenting to be contacted by PerformanceIN regarding our products and services..',
    },
    contactforms: {
      type: 'array',
    },
    contactform_value: {
      type: 'string',
    },
  };

  registerBlockType('performancein/download-form', {
    title: __('Download Form'),
    icon: RequestFormIcon,
    category: 'performancein',
    keywords: [__('Request'), __('form')],
    attributes: allAttr,
    edit: PerformanceinDownloadForm,
    save() {
      return null;
    },
  });
})(wp.i18n, wp.blocks, wp.element, wp.editor, wp.components);
