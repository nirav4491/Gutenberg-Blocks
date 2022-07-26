import {ArticleIcon} from '../icons';

const {__} = wp.i18n;
const {registerBlockType} = wp.blocks;
const {apiFetch} = wp;
const {Fragment} = wp.element;
const {
  RichText,
  InspectorControls,
  MediaUpload,
  BlockControls,
  InnerBlocks,
  AlignmentToolbar,
  PanelColorSettings,
} = wp.editor;
const {
  PanelBody,
  TextControl,
  Button,
  SelectControl,
  RangeControl,
  ToggleControl,
  ServerSideRender,
  ColorPalette,
  TextareaControl,
  RadioControl,
} = wp.components;

import Edit from './edit';

registerBlockType('performancein/partner-listing', {
  title: 'Partner Listing',
  icon: ArticleIcon,
  category: 'performancein',
  keywords: [__('Partner Listing'), __('gutenberg'), __('performancein')],
  example: {
    attributes: {
      caption: __('Partner Listing'),
    },
  },
  attributes: {
    categories: {
      type: 'array',
    },
    categories_value: {
      type: 'string',
    },
    postsToShow: {
      type: 'number',
      default: 1
    },
  },
  edit: Edit,
  save: function(props) {
    // Rendering in PHP
    return null;
  },
});
