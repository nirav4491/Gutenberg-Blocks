import './editor.css';
import './front.css';
import {ArticleIcon} from '../icons';

(function (wpI18n, wpBlocks, wpEditor, wpComponents, wpElement) {
    const {__} = wpI18n;
    const {registerBlockType} = wpBlocks;
    const {Fragment, Component} = wpElement;
    const {RichText,MediaUpload} = wpEditor;
    const {DateTimePicker,Button} = wpComponents;

    class ItemComponent extends Component {
        componentDidMount() {
            const {products} = this.props.attributes;
            if (0 === products.length) {
                this.initList();
            }
        }

        initList() {
            const {products} = this.props.attributes;
            const {setAttributes} = this.props;

            setAttributes({
                products: [
                    ...products,
                    {
                        index: products.length,
                        sourceName: '',
                        linkedinUrl: '',
                        jobTitle: '',
                        companyName: '',
                        jobContent: '',
                        headline: '',
                        articleLink: '',
                        mediaID:'',
                        mediaURL:''
                    }
                ]
            });
        }

        render() {
            const {attributes, setAttributes} = this.props;
            const {products} = attributes;
            const itemList = products
                .sort((a, b) => a.index - b.index)
                .map((product, index) => {
                  return (
                        <div className="col-xs-12 col-md-12 top50Person">
                          <div className="box">
                            <div className="row center-xs start-sm middle-xs">
                        <span
                            className="remove"
                            onClick={() => {
                                const qewQusote = products
                                    .filter(item => item.index !== product.index)
                                    .map(t => {
                                        if (t.index > product.index) {
                                            t.index -= 1;
                                        }

                                        return t;
                                    });

                                setAttributes({
                                    products: qewQusote
                                });
                            }}
                        >
                        <span className="dashicons dashicons-no-alt"></span>
                        </span>
                              <div className="col-xs-10 col-sm-4 col-md-2 col-lg-2 headshotContainer">
                                <div className="box">
                                    <RichText
                                      tagName="a"
                                      placeholder={__('Linkedin URL')}
                                      value={product.linkedinUrl}
                                      className="headshot"
                                      keepPlaceholderOnFocus="true"
                                      onChange={linkedinUrl => {
                                        const newObject = Object.assign({}, product, {
                                          linkedinUrl: linkedinUrl
                                        });
                                        setAttributes({
                                          products: [
                                            ...products.filter(
                                              item => item.index !== product.index
                                            ),
                                            newObject
                                          ]
                                        });
                                      }}
                                    />
                                    <MediaUpload
                                      onSelect={media => {
                                        const imageObject = Object.assign({}, product, {
                                          mediaURL: media.url
                                        });
                                        setAttributes({
                                          products: [
                                            ...products.filter(
                                              item => item.index !== product.index
                                            ),
                                            imageObject
                                          ]
                                        })
                                      }}
                                      allowedTypes="image"
                                      value={  product.mediaURL }
                                      render={ ( { open } ) => (
                                        <Button className={ product.mediaURL? 'image-button  avtar-btn' : 'button button-large' } onClick={ open }>
                                          { ! product.mediaURL ? __( 'Upload Image', 'performancein-discount-block' ) : <img src={  product.mediaURL } className="headshotimage" /> }
                                        </Button>
                                      ) }
                                    />
                                </div>
                              </div>
                              <div className="col-xs-10 col-sm-8 col-md-4 col-lg-3 details">
                                <div className="box">
                                  <RichText
                                    tagName="h2"
                                    placeholder={__('Source Name')}
                                    value={product.sourceName}
                                    className="sourceName"
                                    keepPlaceholderOnFocus="true"
                                    onChange={sourceName => {
                                      const newObject = Object.assign({}, product, {
                                        sourceName: sourceName
                                      });
                                      setAttributes({
                                        products: [
                                          ...products.filter(
                                            item => item.index !== product.index
                                          ),
                                          newObject
                                        ]
                                      });
                                    }}
                                  />
                                  <RichText
                                    tagName="p"
                                    placeholder={__('Job Title')}
                                    value={product.jobTitle}
                                    className="jobTitle"
                                    keepPlaceholderOnFocus="true"
                                    onChange={jobTitle => {
                                      const newObject = Object.assign({}, product, {
                                        jobTitle: jobTitle
                                      });
                                      setAttributes({
                                        products: [
                                          ...products.filter(
                                            item => item.index !== product.index
                                          ),
                                          newObject
                                        ]
                                      });
                                    }}
                                  />
                                  <RichText
                                    tagName="p"
                                    placeholder={__('Company Name')}
                                    value={product.companyName}
                                    className="companyName"
                                    keepPlaceholderOnFocus="true"
                                    onChange={companyName => {
                                      const newObject = Object.assign({}, product, {
                                        companyName: companyName
                                      });
                                      setAttributes({
                                        products: [
                                          ...products.filter(
                                            item => item.index !== product.index
                                          ),
                                          newObject
                                        ]
                                      });
                                    }}
                                  />
                                </div>
                              </div>
                              <div className="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <div className="box blurb">
                                  <RichText
                                    tagName="p"
                                    placeholder={__('Job Content')}
                                    value={product.jobContent}
                                    className="jobContent"
                                    keepPlaceholderOnFocus="true"
                                    onChange={jobContent => {
                                      const newObject = Object.assign({}, product, {
                                        jobContent: jobContent
                                      });
                                      setAttributes({
                                        products: [
                                          ...products.filter(
                                            item => item.index !== product.index
                                          ),
                                          newObject
                                        ]
                                      });
                                    }}
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    );
                });

            return (
                <div className="container-fluid top50List">
                    <div className="row middle-xs">
                        {itemList}
                        <div className="top50-item additem">
                            <button
                                className="components-button add"
                                onClick={content => {
                                    setAttributes({
                                        products: [
                                            ...products,
                                            {
                                              index: products.length,
                                              sourceName: '',
                                              jobTitle: '',
                                              linkedinUrl: '',
                                              companyName: '',
                                              jobContent: '',
                                              headline: '',
                                              articleLink: '',
                                              mediaID:'',
                                              mediaURL:''
                                            }
                                        ]
                                    });
                                }}
                            >
                                <span className="dashicons dashicons-plus"></span> Add New Item
                            </button>
                        </div>
                    </div>
                </div>
            );
        }
    }

    registerBlockType('performancein/top50', {
        title: __('Top50'),
        description: __('Top50'),
        icon: ArticleIcon,
        category: 'performancein',
        keywords: [__('Top50'), __('gutenberg'), __('performancein')],
        example: {
        attributes: {
          caption: __( 'Top50 Listing' ),
        },
      },
        attributes: {
            products: {
                type: 'array',
                default: []
            }
        },
        edit: ItemComponent,

        save: props => {
            const {attributes} = props;
            const {products} = attributes;

            return (
              <div className="container-fluid top50List">
                <div className="row middle-xs">
                  <div className="row center-xs start-sm middle-xs">
                      {products.map((product, index) => (
                          <Fragment>
                              {product.mediaURL && (
                                  <Fragment>
                                      {product.mediaURL && (
                                        <div className="col-xs-12 col-md-12 top50Person">
                                          <div className="box">
                                            <div className="row center-xs start-sm middle-xs">
                                              <div className="col-xs-10 col-sm-4 col-md-2 col-lg-2 headshotContainer">
                                                <div className="box">
                                                  <a href={product.linkedinUrl} className="headshot">
                                                    <img src={product.mediaURL} className="headshotimage" />
                                                  </a>
                                                </div>
                                              </div>
                                              <div className="col-xs-10 col-sm-8 col-md-4 col-lg-3 details">
                                                <div className="box">
                                                  <h2> {product.sourceName}</h2>
                                                  <p className="jobTitle"> {product.jobTitle}</p>
                                                  <p className="companyName"> {product.companyName}</p>
                                                </div>
                                              </div>
                                              <div className="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                                <div className="box blurb">
                                                  {product.jobContent}
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      )}
                                  </Fragment>
                              )}
                          </Fragment>
                      ))}
                  </div>
                </div>
              </div>
            );
        }
    });
})(wp.i18n, wp.blocks, wp.editor, wp.components, wp.element);
