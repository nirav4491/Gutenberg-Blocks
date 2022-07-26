import {ArticleIcon} from '../icons';

const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;
const {apiFetch} = wp;
const {Fragment} = wp.element;
const {
  InspectorControls,
} = wp.editor;
const {
  PanelBody,
  SelectControl,
  RangeControl,
  ServerSideRender,
  TextareaControl,
  ToggleControl
} = wp.components;

registerBlockType('performancein/article-listing', {
  title: 'Article Listing',
  icon: ArticleIcon,
  category: 'performancein',
  keywords: [__('ArticleFilter'), __('gutenberg'), __('performancein')],
  description: __( 'It showcase article listing ' ),
  example: {
    attributes: {
      caption: __( 'Article Listing' ),
    },
  },
  attributes: {

    number_of_post: {
      type: 'number',
      default: '0',
    },
    post_type: {
      default: '',
      type: 'string',
    },
    post_type_obj: {
      type: 'json',
    },
    post_taxs: {
      default: '',
      type: 'string',
    },
    post_taxs_obj: {
      default: [{label: '--Select Taxonomy--', value: ''}],
      type: 'json',
    },
    post_category: {
      default: '',
      type: 'string',
    },
    post_category_obj: {
      default: [{label: '--Select Category--', value: ''}],
      type: 'json',
    },
    category_description: {
      default: false,
      type: 'boolean',
    },
    exclude_post: {type: 'string'},
    design_option: {
      type: 'string',
      default: '',
    },
  },
  edit: function(props) {
    const {
      attributes: {
        number_of_post,
        post_type,
        post_type_obj,
        post_taxs,
        post_taxs_obj,
        post_category,
        post_category_obj,
        category_description,
        exclude_post,
      },
      setAttributes,
    } = props;
    var get_post_taxs = function get_post_taxs(newContent) {
      setAttributes({post_type: newContent});
      var url = '/wp-json/postfilter_apis/post_taxs?post_type=' + newContent;
      fetch(url).then(response => response.json()).then(json => {
        setAttributes({post_taxs_obj: json});
        setAttributes({post_taxs: ''});
        setAttributes({post_category: ''});
      });
    };
    var get_post_categories = function get_post_categories(newContent) {
      setAttributes({post_taxs: newContent});
      var url = '/wp-json/postfilter_apis/categories?tax=' + newContent;
      fetch(url).then(response => response.json()).then(json => {
        setAttributes({post_category_obj: json});
        setAttributes({post_category: ''});
      });
    };
    var url = '/wp-json/postfilter_apis/posttypes';
    fetch(url).then(response => response.json()).then(json => {
      setAttributes({post_type_obj: json});
    });

    var isCategoriesSelect = function isCategoriesSelect(newContent) {
      setAttributes({post_category: newContent});
    };




    return [
      <Fragment>

        <InspectorControls key="Post Filter">
          <PanelBody title="Select Filter" initialOpen="true">
            <SelectControl
              label="Post type"
              value={ post_type }
              options={ post_type_obj }
              onChange={ get_post_taxs }
            />
            <SelectControl
              label="Taxonomies"
              value={ post_taxs }
              options={ post_taxs_obj }
              onChange={ get_post_categories }
            />
            <SelectControl
              label="Categories"
              value={ post_category }
              options={ post_category_obj }
              onChange={ isCategoriesSelect }
            />
            { post_category &&
            <ToggleControl
                label= "Category description"
                help={__('If you don\'t display the category decription , click to disable.')}
                checked={category_description}
                onChange={ value => setAttributes({category_description: value}) }
            />
            }
            <RangeControl
              label="Number of Posts"
              value={ number_of_post }
              onChange={ value => setAttributes({number_of_post: value}) }
              min={ 0 }
              max={ 100 }
            />
            <TextareaControl
              label="Exclude Posts"
              help="Add post-id here for exclude the post (example 1,2..)"
              tagName="p"
              className="section-title"
              value={ exclude_post }
              onChange={ value => setAttributes({exclude_post: value}) }
            />
          </PanelBody>
        </InspectorControls>
        <ServerSideRender
          block="performancein/article-listing"
          attributes={ {
            post_type: post_type,
            post_taxs: post_taxs,
            post_category: post_category,
            number_of_post: number_of_post,
            category_description:category_description,
            exclude_post: exclude_post,
          } }
        />
      </Fragment>,
    ];
  },
  save: function(props) {
    // Rendering in PHP
    return null;
  },
});
