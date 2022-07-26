import './editor.css';
import './front.css';
import classnames from 'classnames';

(function (wpI18n, wpBlocks, wpEditor, wpComponents, wpElement) {
    const {__} = wpI18n;
    const {registerBlockType} = wpBlocks;
    const {Fragment} = wpElement;
    const {MediaUpload, InspectorControls, InnerBlocks} = wpEditor;
    const {PanelBody, PanelRow, TextControl, Button, SelectControl, RangeControl, ToggleControl, ColorPalette} = wpComponents;
    const multipleBlockIcon = <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 612 612">
        <g>
            <path fill="#FF3C00"
                  d="M1.659,484.737L1.001,206.595c-0.032-13.686,13.95-22.938,26.534-17.559l253.206,108.241c6.997,2.991,11.542,9.859,11.56,17.468l0.658,278.142c0.032,13.687-13.95,22.939-26.534,17.56L13.219,502.206C6.222,499.215,1.676,492.347,1.659,484.737z M581.805,219.687L348.142,320.883l0.608,257.406l233.664-101.196L581.805,219.687 M591.26,186.131c10.043-0.025,19.056,8.054,19.081,19.022l0.658,278.142c0.018,7.609-4.495,14.5-11.478,17.523l-252.69,109.438c-2.493,1.079-5.047,1.583-7.534,1.59c-10.044,0.023-19.058-8.055-19.083-19.022l-0.658-278.143c-0.019-7.609,4.495-14.5,11.479-17.523l252.69-109.437C586.218,186.64,588.771,186.137,591.26,186.131L591.26,186.131z M304.152,29.466L61.767,137.691l242.894,107.075l242.386-108.224L304.152,29.466 M304.083,0c2.632-0.006,5.266,0.533,7.728,1.618l266.403,117.439c15.112,6.663,15.163,28.088,0.082,34.821L312.451,272.577c-2.456,1.097-5.088,1.648-7.721,1.655c-2.632,0.006-5.266-0.533-7.728-1.618L30.6,155.175c-15.113-6.662-15.163-28.088-0.083-34.821L296.361,1.655C298.818,0.558,301.449,0.006,304.083,0L304.083,0z"/>
        </g>
    </svg>;

    /**
     * Block: MultiPurpose Gutenberg Block.
     */
    registerBlockType('performancein/multipurpose-gutenberg-block', {
        title: __('Multi Purpose Block'),
        description: __('Use one block containing multiple elements.'),
        icon: multipleBlockIcon,
        category: 'performancein',
        attributes: {
            ElementTag: {
                type: 'string',
                default: 'div'
            },
            elementID: {
                type: 'string'
            },
            backgroundImage: {
                type: 'string',
                default: ''
            },
            backgroundColor: {
                type: 'string',
                default: ''
            },
            backgroundSize: {
                type: 'boolean',
                default: false,
            },
            backgroundPosition: {
                type: 'string',
                default: '',
            },
            backgroundAttachment: {
                type: 'boolean',
                default: false,
            },
            layout: {
                type: 'string',
                default: ''
            },
            borderStyle: {
                type: 'string',
                default: '',
            },
            borderWidth: {
                type: 'number',
            },
            borderColor: {
                type: 'string',
            },
            borderRadius: {
                type: 'number',
            },
            topBorderStyle: {
                type: 'string',
                default: ''
            },
            topBorderWidth: {
                type: 'number',
            },
            topBorderColor: {
                type: 'string',
            },
            topBorderRadius: {
                type: 'number',
            },
            bottomBorderStyle: {
                type: 'string',
                default: ''
            },
            bottomBorderWidth: {
                type: 'number',
            },
            bottomBorderColor: {
                type: 'string',
            },
            bottomBorderRadius: {
                type: 'number',
            },
            rightBorderStyle: {
                type: 'string',
                default: ''
            },
            rightBorderWidth: {
                type: 'number',
            },
            rightBorderColor: {
                type: 'string',
            },
            rightBorderRadius: {
                type: 'number',
            },
            leftBorderStyle: {
                type: 'string',
                default: ''
            },
            leftBorderWidth: {
                type: 'number',
            },
            leftBorderColor: {
                type: 'string',
            },
            leftBorderRadius: {
                type: 'number',
            },
            blockAlign: {
                type: 'string',
                default: 'center'
            },
            textAlign: {
                type: 'string',
                default: ''
            },
            width: {
                type: 'string',
                default: ''
            },
            height: {
                type: 'string',
                default: ''
            },
            opacity: {
                type: 'number',
                default: 0
            },
            overlayColor: {
                type: 'string',
            },
            paddingTop: {
                type: 'string',
                default: ''
            },
            paddingRight: {
                type: 'string',
                default: ''
            },
            paddingBottom: {
                type: 'string',
                default: ''

            },
            paddingLeft: {
                type: 'string',
                default: ''
            },
            marginTop: {
                type: 'string',
                default: ''
            },
            marginRight: {
                type: 'string',
                default: ''
            },
            marginBottom: {
                type: 'string',
                default: ''

            },
            marginLeft: {
                type: 'string',
                default: ''
            },
            gradientRange1: {
                type: 'number',
                default: 0
            },
            gradientRange2: {
                type: 'number',
                default: 0
            },
            gradientRange3: {
                type: 'number',
                default: 0
            },
            color1: {
                type: 'string',
                default: '#fff'
            },
            color2: {
                type: 'string',
                default: '#fff'
            },
            color3: {
                type: 'string',
                default: '#fff'
            },
            gradientType: {
                type: 'string',
                default: ''
            },
            ToggleInserter: {
                type: 'boolean',
                default: false
            }
        },
        edit(props) {
            const {attributes, setAttributes, className} = props;
            const {
                backgroundImage,
                backgroundColor,
                backgroundSize,
                backgroundPosition,
                backgroundAttachment,
                layout,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                blockAlign,
                textAlign,
                width,
                height,
                opacity,
                overlayColor,
                paddingTop,
                paddingRight,
                paddingBottom,
                paddingLeft,
                marginTop,
                marginRight,
                marginBottom,
                marginLeft,
                gradientRange1,
                gradientRange2,
                gradientRange3,
                color1,
                color2,
                color3,
                gradientType,
                topBorderStyle,
                topBorderWidth,
                topBorderColor,
                topBorderRadius,
                bottomBorderStyle,
                bottomBorderWidth,
                bottomBorderColor,
                bottomBorderRadius,
                rightBorderStyle,
                rightBorderWidth,
                rightBorderColor,
                rightBorderRadius,
                leftBorderStyle,
                leftBorderWidth,
                leftBorderColor,
                leftBorderRadius,
                ElementTag,
                elementID,
                ToggleInserter
            } = attributes;
            const onSelectLayout = event => {
                const selectedLayout = event.target.value;
                const selectedClass = event.target.className;
                'components-button button has-tooltip active' === selectedClass && setAttributes({layout: ''});
                'components-button button has-tooltip active' !== selectedClass && setAttributes({layout: selectedLayout ? selectedLayout : ''});
            };

            const onSelectTagType = event => {
                setAttributes({ElementTag: event.target.value ? event.target.value : 'div'});
            };

            const classes = classnames(
                className,
                layout && `has-${layout}`,
                blockAlign && `is-block-${blockAlign}`,
                width && 'has-custom-width',
                {
                    'has-background-size': backgroundSize,
                    'has-background-attachment': backgroundAttachment,
                    'has-background-opacity': 0 !== opacity,

                },
                opacityRatioToClass(opacity)
            );
            const style = {};
            backgroundImage && (style.backgroundImage = `url(${backgroundImage})`);
            backgroundColor && (style.backgroundColor = backgroundColor);
            backgroundPosition && (style.backgroundPosition = backgroundPosition);
            textAlign && (style.textAlign = textAlign);
            width && (style.width = width + '%');
            height && (style.height = height + 'px');
            overlayColor && (style.backgroundColor = overlayColor);
            paddingTop && (style.paddingTop = paddingTop + 'px');
            paddingRight && (style.paddingRight = paddingRight + 'px');
            paddingBottom && (style.paddingBottom = paddingBottom + 'px');
            paddingLeft && (style.paddingLeft = paddingLeft + 'px');
            marginTop && (style.marginTop = marginTop + 'px');
            marginRight && (style.marginRight = marginRight + 'px');
            marginBottom && (style.marginBottom = marginBottom + 'px');
            marginLeft && (style.marginLeft = marginLeft + 'px');
            (gradientType && ('#fff' !== color1 || '#fff' !== color2 || '#fff' !== color3)) && (style.background = 'linear-gradient(' + gradientType + ', ' + color1 + ' ' + gradientRange1 + '%, ' + color2 + ' ' + gradientRange2 + '%, ' + color3 + ' ' + gradientRange3 + '%)');


            marginTop && (style.marginTop = marginTop + 'px');
            if (borderStyle) {
                style.borderStyle = borderStyle;
                if (borderWidth) {
                    style.borderWidth = borderWidth + 'px';
                }
                if (borderColor) {
                    style.borderColor = borderColor;
                }
                if (borderRadius) {
                    style.borderRadius = borderRadius;
                }
            } else {
                if (topBorderStyle) {
                    style.borderTopStyle = topBorderStyle;
                    if (topBorderWidth) {
                        style.borderTopWidth = topBorderWidth + 'px';
                    }
                    if (topBorderColor) {
                        style.borderTopColor = topBorderColor;
                    }
                    if (topBorderRadius) {
                        style.borderTopLeftRadius = topBorderRadius;
                    }
                }
                if (bottomBorderStyle) {
                    style.borderBottomStyle = bottomBorderStyle;
                    if (bottomBorderWidth) {
                        style.borderBottomWidth = bottomBorderWidth + 'px';
                    }
                    if (bottomBorderColor) {
                        style.borderBottomColor = bottomBorderColor;
                    }
                    if (bottomBorderRadius) {
                        style.borderBottomRightRadius = bottomBorderRadius;
                    }
                }
                if (rightBorderStyle) {
                    style.borderRightStyle = rightBorderStyle;
                    if (rightBorderWidth) {
                        style.borderRightWidth = rightBorderWidth + 'px';
                    }
                    if (rightBorderColor) {
                        style.borderRightColor = rightBorderColor;
                    }
                    if (rightBorderRadius) {
                        style.borderTopRightRadius = rightBorderRadius;
                    }
                }
                if (leftBorderStyle) {
                    style.borderLeftStyle = leftBorderStyle;
                    if (leftBorderWidth) {
                        style.borderLeftWidth = leftBorderWidth + 'px';
                    }
                    if (leftBorderColor) {
                        style.borderLeftColor = leftBorderColor;
                    }
                    if (leftBorderRadius) {
                        style.borderBottomLeftRadius = leftBorderRadius;
                    }
                }
            }

            return [
                (
                    <InspectorControls>
                        <div className="custom-inspactor-setting">
                            <div className="full-width mt30">
                                <ToggleControl
                                    label={__('Toggle Inserter')}
                                    checked={!!ToggleInserter}
                                    onChange={() => setAttributes({ToggleInserter: !ToggleInserter})}
                                />
                            </div>
                            <PanelBody title={__('Wrapper')} initialOpen={false}>
                                <Button
                                    className={'header' === ElementTag ? 'button active' : 'button'}
                                    onClick={onSelectTagType}
                                    value="header">
                                    {__('Header')}
                                </Button>
                                <Button
                                    className={'section' === ElementTag ? 'button active' : 'button'}
                                    onClick={onSelectTagType}
                                    value="section">
                                    {__('Section')}
                                </Button>
                                <TextControl
                                    label="Wrapper Tag ID Attribute"
                                    type="string"
                                    placeHolder="id"
                                    value={elementID}
                                    onChange={(value) => setAttributes({elementID: value})}
                                />
                            </PanelBody>
                            <PanelBody title={__('Page Layout')} initialOpen={false}>
                                <Button
                                    className={'full' === layout ? 'button has-tooltip active' : 'button has-tooltip'}
                                    onClick={onSelectLayout}
                                    data-tooltip="This layout is for full width (width:100%)."
                                    value="full"
                                >
                                    {__('Full Width')}
                                </Button>
                                <Button
                                    className={'fixed' === layout ? 'button has-tooltip active' : 'button has-tooltip'}
                                    onClick={onSelectLayout}
                                    data-tooltip="This layout is for fixed width (width:1200px)."
                                    value="fixed">
                                    {__('Fixed')}
                                </Button>
                                <Button
                                    className={'semi' === layout ? 'button has-tooltip active' : 'button has-tooltip'}
                                    onClick={onSelectLayout}
                                    data-tooltip="This layout is for Semi width (width:85%)."
                                    value="semi">
                                    {__('Semi')}
                                </Button>
                            </PanelBody>
                            <PanelBody title={__('Background')} initialOpen={false} className="bg-setting">
                                <PanelBody title={__('Background Image')} initialOpen={false} className="bg-setting bg-img-setting">
                                    <MediaUpload
                                        onSelect={backgroundImage => setAttributes({
                                            backgroundImage: backgroundImage.sizes.full.url ? backgroundImage.sizes.full.url : '',
                                            backgroundColor: ''
                                        })}
                                        type="image"
                                        value={backgroundImage}
                                        render={({open}) => (
                                            <Button
                                                className={backgroundImage ? 'image-button' : 'button button-large'}
                                                onClick={open}>
                                                {!backgroundImage ? __('Upload Image') :
                                                    <div style={{
                                                        backgroundImage: `url(${backgroundImage})`,
                                                        backgroundSize: 'cover',
                                                        backgroundPosition: 'center',
                                                        height: '150px',
                                                        width: '225px'
                                                    }}>
                                                    </div>}
                                            </Button>
                                        )}
                                    />
                                    {backgroundImage ? <Button
                                        className="button"
                                        onClick={() => setAttributes({backgroundImage: '', overlayColor: ''})}>
                                        {__('Remove Background Image')}
                                    </Button> : null
                                    }
                                    {backgroundImage && (
                                        <Fragment>
                                            <ToggleControl
                                                label={__('Background Size ON - Set background size "Cover"')}
                                                checked={backgroundSize}
                                                onChange={() => setAttributes({backgroundSize: !backgroundSize})}
                                            />
                                            <ToggleControl
                                                label={__('Background Attachment ON - Set background attachment "Fixed" ')}
                                                checked={backgroundAttachment}
                                                onChange={() => setAttributes({backgroundAttachment: !backgroundAttachment})}
                                            />
                                            <SelectControl
                                                label={__('Select Position')}
                                                value={backgroundPosition}
                                                options={[
                                                    {label: __('Bottom'), value: 'bottom'},
                                                    {label: __('Center'), value: 'center'},
                                                    {label: __('Inherit'), value: 'inherit'},
                                                    {label: __('Initial'), value: 'initial'},
                                                    {label: __('Left'), value: 'left'},
                                                    {label: __('Right'), value: 'right'},
                                                    {label: __('Top'), value: 'top'},
                                                    {label: __('Unset'), value: 'unset'},
                                                ]}
                                                onChange={(value) => setAttributes({backgroundPosition: value})}
                                            />
                                            <div className="inspector-field inspector-field-color components-base-control">
                                                <label className="inspector-mb-0">Overlay</label>
                                                <div className="inspector-ml-auto">
                                                    <ColorPalette
                                                        value={overlayColor}
                                                        onChange={(value) => setAttributes({overlayColor: value})}
                                                    />
                                                </div>
                                            </div>
                                            <div className="inspector-field inspector-border-radius components-base-control">
                                                <label>Background Opacity</label>
                                                <RangeControl
                                                    value={opacity}
                                                    min={0}
                                                    max={100}
                                                    step={10}
                                                    onChange={(ratio) => setAttributes({opacity: ratio})}
                                                />
                                            </div>
                                        </Fragment>
                                    )}
                                </PanelBody>
                                {(
                                    <PanelBody title={__('Background Color')} initialOpen={false} className="bg-setting">
                                        <PanelRow>
                                            <div className="inspector-field inspector-field-color ">
                                                <label className="inspector-mb-0">Background Color</label>
                                                <div className="inspector-ml-auto">
                                                    <ColorPalette
                                                        value={backgroundColor}
                                                        onChange={(value) => setAttributes({backgroundColor: value ? value : '', opacity: 0})}
                                                    />
                                                </div>
                                            </div>
                                        </PanelRow>
                                    </PanelBody>
                                )}
                                <PanelBody title={__('Gradient Background')} initialOpen={false} className="bg-setting gredient-setting">
                                    <SelectControl
                                        label={__('Select Gradient Type')}
                                        value={gradientType}
                                        options={[
                                            {label: __('Select Type'), value: ''},
                                            {label: __('bottom'), value: 'to bottom'},
                                            {label: __('Top'), value: 'to top'},
                                            {label: __('Right'), value: 'to right'},
                                            {label: __('Left'), value: 'to left'},
                                            {label: __('Top Left'), value: 'to top left'},
                                            {label: __('Bottom Left'), value: 'to bottom left'},
                                            {label: __('Top Right'), value: 'to top right'},
                                            {label: __('Bottom Right'), value: 'to bottom right'},
                                        ]}
                                        onChange={(value) => setAttributes({gradientType: value})}
                                    />
                                    {gradientType && (
                                        <Fragment>
                                            <h3>{__('Gradient Fill 1')}</h3>
                                            <div className="inspector-field inspector-field-color components-base-control gradientcolor">
                                                <label className="inspector-mb-0">Color</label>
                                                <div className="inspector-ml-auto">
                                                    <ColorPalette
                                                        value={color1}
                                                        onChange={(value) => setAttributes({color1: value ? value : '#fff'})}
                                                    />
                                                </div>
                                            </div>
                                            <div className="inspector-field inspector-border-radius components-base-control">
                                                <label>Range</label>
                                                <RangeControl
                                                    value={gradientRange1}
                                                    min={0}
                                                    max={100}
                                                    step={10}
                                                    onChange={(value) => setAttributes({gradientRange1: value})}
                                                />
                                            </div>
                                            <h3>{__('Gradient Fill 2')}</h3>
                                            <div className="inspector-field inspector-field-color components-base-control gradientcolor">
                                                <label className="inspector-mb-0">Color</label>
                                                <div className="inspector-ml-auto">
                                                    <ColorPalette
                                                        value={color2}
                                                        onChange={(value) => setAttributes({color2: value ? value : '#fff'})}
                                                    />
                                                </div>
                                            </div>
                                            <div className="inspector-field inspector-border-radius components-base-control">
                                                <label>Range</label>
                                                <RangeControl
                                                    value={gradientRange2}
                                                    min={0}
                                                    max={100}
                                                    step={10}
                                                    onChange={(value) => setAttributes({gradientRange2: value})}
                                                />
                                            </div>
                                            <h3>{__('Gradient Fill 3')}</h3>
                                            <div className="inspector-field inspector-field-color components-base-control gradientcolor">
                                                <label className="inspector-mb-0">Color</label>
                                                <div className="inspector-ml-auto">
                                                    <ColorPalette
                                                        value={color3}
                                                        onChange={(value) => setAttributes({color3: value ? value : '#fff'})}
                                                    />
                                                </div>
                                            </div>
                                            <div className="inspector-field inspector-border-radius components-base-control">
                                                <label>Range</label>
                                                <RangeControl
                                                    value={gradientRange3}
                                                    min={0}
                                                    max={100}
                                                    step={10}
                                                    onChange={(value) => setAttributes({gradientRange3: value})}
                                                />
                                            </div>
                                        </Fragment>
                                    )}
                                </PanelBody>
                            </PanelBody>
                            <PanelBody title={__('Border')} initialOpen={false} className="border-setting">
                                <PanelBody title={__('All Border')} initialOpen={false} className="border-setting">
                                    <PanelRow>
                                        <div className="inspector-field inspector-border-style">
                                            <label>Border Style</label>
                                            <div className="inspector-field-button-list inspector-field-button-list-fluid">
                                                <button className={'solid' === borderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'solid'})}><span className="inspector-field-border-type inspector-field-border-type-solid"></span></button>
                                                <button className={'dotted' === borderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'dotted'})}><span className="inspector-field-border-type inspector-field-border-type-dotted"></span></button>
                                                <button className={'dashed' === borderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'dashed'})}><span className="inspector-field-border-type inspector-field-border-type-dashed"></span></button>
                                                <button className={'none' === borderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'none'})}><span className="inspector-field-border-type inspector-field-border-type-none"><i className="fa fa-ban"></i></span></button>
                                            </div>
                                        </div>
                                    </PanelRow>
                                    {borderStyle && (
                                        <Fragment>
                                            <PanelRow>
                                                <div className="inspector-field inspector-field-color ">
                                                    <label className="inspector-mb-0">Color</label>
                                                    <div className="inspector-ml-auto">
                                                        <ColorPalette
                                                            value={borderColor}
                                                            onChange={borderColor => setAttributes({borderColor: borderColor})}
                                                        />
                                                    </div>
                                                </div>
                                            </PanelRow>
                                            <PanelRow>
                                                <div className="inspector-field inspector-border-width">
                                                    <label>Border Width</label>
                                                    <RangeControl
                                                        value={borderWidth ? borderWidth : 0}
                                                        min={0}
                                                        max={10}
                                                        onChange={(value) => setAttributes({borderWidth: value})}
                                                    />
                                                </div>
                                            </PanelRow>
                                            <PanelRow>
                                                <div className="inspector-field inspector-border-width">
                                                    <label>Border Radius</label>
                                                    <RangeControl
                                                        value={borderRadius ? borderRadius : 0}
                                                        min={0}
                                                        max={100}
                                                        onChange={(value) => setAttributes({borderRadius: value})}
                                                    />
                                                </div>
                                            </PanelRow>
                                        </Fragment>
                                    )}
                                </PanelBody>
                                {!borderStyle && (
                                    <PanelBody title={__('Top Border')} initialOpen={false} className="border-setting">
                                        <PanelRow>
                                            <div className="inspector-field inspector-border-style">
                                                <label>Border Style</label>
                                                <div className="inspector-field-button-list inspector-field-button-list-fluid">
                                                    <button className={'solid' === topBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({topBorderStyle: 'solid'})}><span className="inspector-field-border-type inspector-field-border-type-solid"></span></button>
                                                    <button className={'dotted' === topBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({topBorderStyle: 'dotted'})}><span className="inspector-field-border-type inspector-field-border-type-dotted"></span></button>
                                                    <button className={'dashed' === topBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({topBorderStyle: 'dashed'})}><span className="inspector-field-border-type inspector-field-border-type-dashed"></span></button>
                                                    <button className={'none' === topBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'none'})}><span className="inspector-field-border-type inspector-field-border-type-none"><i className="fa fa-ban"></i></span></button>
                                                </div>
                                            </div>
                                        </PanelRow>
                                        {topBorderStyle && (
                                            <Fragment>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-field-color ">
                                                        <label className="inspector-mb-0">Color</label>
                                                        <div className="inspector-ml-auto">
                                                            <ColorPalette
                                                                value={topBorderColor}
                                                                onChange={topBorderColor => setAttributes({topBorderColor: topBorderColor})}
                                                            />
                                                        </div>
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Width</label>
                                                        <RangeControl
                                                            value={topBorderWidth ? topBorderWidth : 0}
                                                            min={0}
                                                            max={10}
                                                            onChange={(value) => setAttributes({topBorderWidth: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Radius</label>
                                                        <RangeControl
                                                            value={topBorderRadius ? topBorderRadius : 0}
                                                            min={0}
                                                            max={100}
                                                            onChange={(value) => setAttributes({topBorderRadius: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                            </Fragment>
                                        )}
                                    </PanelBody>
                                )}
                                {!borderStyle && (
                                    <PanelBody title={__('Right Border')} initialOpen={false} className="border-setting">
                                        <PanelRow>
                                            <div className="inspector-field inspector-border-style">
                                                <label>Border Style</label>
                                                <div className="inspector-field-button-list inspector-field-button-list-fluid">
                                                    <button className={'solid' === rightBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({rightBorderStyle: 'solid'})}><span className="inspector-field-border-type inspector-field-border-type-solid"></span></button>
                                                    <button className={'dotted' === rightBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({rightBorderStyle: 'dotted'})}><span className="inspector-field-border-type inspector-field-border-type-dotted"></span></button>
                                                    <button className={'dashed' === rightBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({rightBorderStyle: 'dashed'})}><span className="inspector-field-border-type inspector-field-border-type-dashed"></span></button>
                                                    <button className={'none' === rightBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'none'})}><span className="inspector-field-border-type inspector-field-border-type-none"><i className="fa fa-ban"></i></span></button>
                                                </div>
                                            </div>
                                        </PanelRow>
                                        {rightBorderStyle && (
                                            <Fragment>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-field-color ">
                                                        <label className="inspector-mb-0">Color</label>
                                                        <div className="inspector-ml-auto">
                                                            <ColorPalette
                                                                value={rightBorderColor}
                                                                onChange={rightBorderColor => setAttributes({rightBorderColor: rightBorderColor})}
                                                            />
                                                        </div>
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Width</label>
                                                        <RangeControl
                                                            value={rightBorderWidth ? rightBorderWidth : 0}
                                                            min={0}
                                                            max={10}
                                                            onChange={(value) => setAttributes({rightBorderWidth: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Radius</label>
                                                        <RangeControl
                                                            value={rightBorderRadius ? rightBorderRadius : 0}
                                                            min={0}
                                                            max={100}
                                                            onChange={(value) => setAttributes({rightBorderRadius: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                            </Fragment>
                                        )}
                                    </PanelBody>
                                )}
                                {!borderStyle && (
                                    <PanelBody title={__('Bottom Border')} initialOpen={false} className="border-setting">
                                        <PanelRow>
                                            <div className="inspector-field inspector-border-style">
                                                <label>Border Style</label>
                                                <div className="inspector-field-button-list inspector-field-button-list-fluid">
                                                    <button className={'solid' === bottomBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({bottomBorderStyle: 'solid'})}><span className="inspector-field-border-type inspector-field-border-type-solid"></span></button>
                                                    <button className={'dotted' === bottomBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({bottomBorderStyle: 'dotted'})}><span className="inspector-field-border-type inspector-field-border-type-dotted"></span></button>
                                                    <button className={'dashed' === bottomBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({bottomBorderStyle: 'dashed'})}><span className="inspector-field-border-type inspector-field-border-type-dashed"></span></button>
                                                    <button className={'none' === bottomBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'none'})}><span className="inspector-field-border-type inspector-field-border-type-none"><i className="fa fa-ban"></i></span></button>
                                                </div>
                                            </div>
                                        </PanelRow>
                                        {bottomBorderStyle && (
                                            <Fragment>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-field-color ">
                                                        <label className="inspector-mb-0">Color</label>
                                                        <div className="inspector-ml-auto">
                                                            <ColorPalette
                                                                value={bottomBorderColor}
                                                                onChange={bottomBorderColor => setAttributes({bottomBorderColor: bottomBorderColor})}
                                                            />
                                                        </div>
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Width</label>
                                                        <RangeControl
                                                            value={bottomBorderWidth ? bottomBorderWidth : 0}
                                                            min={0}
                                                            max={10}
                                                            onChange={(value) => setAttributes({bottomBorderWidth: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Radius</label>
                                                        <RangeControl
                                                            value={bottomBorderRadius ? bottomBorderRadius : 0}
                                                            min={0}
                                                            max={100}
                                                            onChange={(value) => setAttributes({bottomBorderRadius: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                            </Fragment>
                                        )}
                                    </PanelBody>
                                )}
                                {!borderStyle && (
                                    <PanelBody title={__('Left Border')} initialOpen={false} className="border-setting">
                                        <PanelRow>
                                            <div className="inspector-field inspector-border-style">
                                                <label>Border Style</label>
                                                <div className="inspector-field-button-list inspector-field-button-list-fluid">
                                                    <button className={'solid' === leftBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({leftBorderStyle: 'solid'})}><span className="inspector-field-border-type inspector-field-border-type-solid"></span></button>
                                                    <button className={'dotted' === leftBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({leftBorderStyle: 'dotted'})}><span className="inspector-field-border-type inspector-field-border-type-dotted"></span></button>
                                                    <button className={'dashed' === leftBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({leftBorderStyle: 'dashed'})}><span className="inspector-field-border-type inspector-field-border-type-dashed"></span></button>
                                                    <button className={'none' === leftBorderStyle ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({borderStyle: 'none'})}><span className="inspector-field-border-type inspector-field-border-type-none"><i className="fa fa-ban"></i></span></button>
                                                </div>
                                            </div>
                                        </PanelRow>
                                        {leftBorderStyle && (
                                            <Fragment>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-field-color ">
                                                        <label className="inspector-mb-0">Color</label>
                                                        <div className="inspector-ml-auto">
                                                            <ColorPalette
                                                                value={leftBorderColor}
                                                                onChange={leftBorderColor => setAttributes({leftBorderColor: leftBorderColor})}
                                                            />
                                                        </div>
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Width</label>
                                                        <RangeControl
                                                            value={leftBorderWidth ? leftBorderWidth : 0}
                                                            min={1}
                                                            max={10}
                                                            onChange={(value) => setAttributes({leftBorderWidth: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                                <PanelRow>
                                                    <div className="inspector-field inspector-border-width">
                                                        <label>Border Radius</label>
                                                        <RangeControl
                                                            value={leftBorderRadius ? leftBorderRadius : 0}
                                                            min={0}
                                                            max={100}
                                                            onChange={(value) => setAttributes({leftBorderRadius: value})}
                                                        />
                                                    </div>
                                                </PanelRow>
                                            </Fragment>
                                        )}
                                    </PanelBody>
                                )}
                            </PanelBody>
                            <PanelBody title={__('Dimensions')} initialOpen={false}>
                                <PanelRow>
                                    <div className="inspector-field alignment-settings">
                                        <div className="alignment-wrapper">
                                            <TextControl
                                                label="Width"
                                                type="number"
                                                placeHolder="Width (%)"
                                                value={width}
                                                min="1"
                                                max="100"
                                                step="1"
                                                onChange={(value) => setAttributes({width: value})}
                                            />
                                        </div>
                                        <div className="alignment-wrapper">
                                            <TextControl
                                                label="Height"
                                                type="number"
                                                min="1"
                                                placeHolder="Height (px)"
                                                value={height}
                                                onChange={(value) => setAttributes({height: value})}
                                            />
                                        </div>
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div className="inspector-field inspector-field-transform">
                                        <label className="mt10">Text Transform</label>
                                        <div className="inspector-field-button-list inspector-field-button-list-fluid inspector-ml-auto">
                                            <button className={'none' === blockAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({blockAlign: 'none'})}><i className="fa fa-ban"></i></button>
                                            <button className={'lowercase' === blockAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({blockAlign: 'lowercase'})}><span>aa</span></button>
                                            <button className={'capitalize' === blockAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({blockAlign: 'capitalize'})}><span>Aa</span></button>
                                            <button className={'uppercase' === blockAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({blockAlign: 'uppercase'})}><span>AA</span></button>
                                        </div>
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div className="inspector-field inspector-field-alignment">
                                        <label className="inspector-mb-0">Alignment</label>
                                        <div className="inspector-field-button-list inspector-field-button-list-fluid">
                                            <button className={'left' === textAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({textAlign: 'left'})}><i className="fa fa-align-left"></i></button>
                                            <button className={'center' === textAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({textAlign: 'center'})}><i className="fa fa-align-center"></i></button>
                                            <button className={'right' === textAlign ? 'active inspector-button' : ' inspector-button'} onClick={() => setAttributes({textAlign: 'right'})}><i className="fa fa-align-right"></i></button>
                                        </div>
                                    </div>
                                </PanelRow>
                            </PanelBody>
                            <PanelBody title="Spacing" initialOpen={false}>
                                <PanelRow>
                                    <div className="inspector-field inspector-field-padding">
                                        <label className="mt10">Padding</label>
                                        <div className="padding-setting">
                                            <div className="col-main-4">
                                                <div className="padd-top col-main-inner" data-tooltip="padding Top">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={paddingTop}
                                                        onChange={(value) => setAttributes({paddingTop: value})}
                                                    />
                                                    <label>Top</label>
                                                </div>
                                                <div className="padd-buttom col-main-inner" data-tooltip="padding Bottom">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={paddingBottom}
                                                        onChange={(value) => setAttributes({paddingBottom: value})}
                                                    />
                                                    <label>Bottom</label>
                                                </div>
                                                <div className="padd-left col-main-inner" data-tooltip="padding Left">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={paddingLeft}
                                                        onChange={(value) => setAttributes({paddingLeft: value})}
                                                    />
                                                    <label>Left</label>
                                                </div>
                                                <div className="padd-right col-main-inner" data-tooltip="padding Right">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={paddingRight}
                                                        onChange={(value) => setAttributes({paddingRight: value})}
                                                    />
                                                    <label>Right</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </PanelRow>
                                <PanelRow>
                                    <div className="inspector-field inspector-field-margin">
                                        <label className="mt10">Margin</label>
                                        <div className="margin-setting">
                                            <div className="col-main-4">
                                                <div className="padd-top col-main-inner" data-tooltip="margin Top">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={marginTop}
                                                        onChange={(value) => setAttributes({marginTop: value})}
                                                    />
                                                    <label>Top</label>
                                                </div>
                                                <div className="padd-buttom col-main-inner" data-tooltip="margin Bottom">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={marginBottom}
                                                        onChange={(value) => setAttributes({marginBottom: value})}
                                                    />
                                                    <label>Bottom</label>
                                                </div>
                                                <div className="padd-left col-main-inner" data-tooltip="margin Left">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={marginLeft}
                                                        onChange={(value) => setAttributes({marginLeft: value})}
                                                    />
                                                    <label>Left</label>
                                                </div>
                                                <div className="padd-right col-main-inner" data-tooltip="margin Right">
                                                    <TextControl
                                                        type="number"
                                                        min="1"
                                                        value={marginRight}
                                                        onChange={(value) => setAttributes({marginRight: value})}
                                                    />
                                                    <label>Right</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </PanelRow>
                            </PanelBody>
                        </div>
                    </InspectorControls>
                ),
                (
                    <ElementTag id={elementID} className={`${classes} ${ToggleInserter ? 'performancein-inserter-on' : 'performancein-inserter-off'}`} style={style}>
                        <InnerBlocks/>
                    </ElementTag>
                ),
            ];
        },
        save(props) {
            const {attributes, className} = props;
            const {
                backgroundImage,
                backgroundColor,
                backgroundSize,
                backgroundPosition,
                backgroundAttachment,
                layout,
                borderStyle,
                borderWidth,
                borderColor,
                borderRadius,
                blockAlign,
                textAlign,
                width,
                height,
                opacity,
                overlayColor,
                paddingTop,
                paddingRight,
                paddingBottom,
                paddingLeft,
                marginTop,
                marginRight,
                marginBottom,
                marginLeft,
                gradientRange1,
                gradientRange2,
                gradientRange3,
                color1,
                color2,
                color3,
                gradientType,
                topBorderStyle,
                topBorderWidth,
                topBorderColor,
                topBorderRadius,
                bottomBorderStyle,
                bottomBorderWidth,
                bottomBorderColor,
                bottomBorderRadius,
                rightBorderStyle,
                rightBorderWidth,
                rightBorderColor,
                rightBorderRadius,
                leftBorderStyle,
                leftBorderWidth,
                leftBorderColor,
                leftBorderRadius,
                ElementTag,
                elementID
            } = attributes;
            const classes = classnames(
                className,

                layout && `has-${layout}`,
                blockAlign && `is-block-${blockAlign}`,
                width && 'has-custom-width',
                {
                    'has-background-size': backgroundSize,
                    'has-background-attachment': backgroundAttachment,
                    'has-background-opacity': 0 !== opacity,

                },
                opacityRatioToClass(opacity)
            );
            const style = {};
            backgroundImage && (style.backgroundImage = `url(${backgroundImage})`);
            backgroundColor && (style.backgroundColor = backgroundColor);
            backgroundPosition && (style.backgroundPosition = backgroundPosition);
            textAlign && (style.textAlign = textAlign);
            width && (style.width = width + '%');
            height && (style.height = height + 'px');
            overlayColor && (style.backgroundColor = overlayColor);
            paddingTop && (style.paddingTop = paddingTop + 'px');
            paddingRight && (style.paddingRight = paddingRight + 'px');
            paddingBottom && (style.paddingBottom = paddingBottom + 'px');
            paddingLeft && (style.paddingLeft = paddingLeft + 'px');
            marginTop && (style.marginTop = marginTop + 'px');
            marginRight && (style.marginRight = marginRight + 'px');
            marginBottom && (style.marginBottom = marginBottom + 'px');
            marginLeft && (style.marginLeft = marginLeft + 'px');
            (gradientType && ('#fff' !== color1 || '#fff' !== color2 || '#fff' !== color3)) && (style.background = 'linear-gradient(' + gradientType + ', ' + color1 + ' ' + gradientRange1 + '%, ' + color2 + ' ' + gradientRange2 + '%, ' + color3 + ' ' + gradientRange3 + '%)');
            marginTop && (style.marginTop = marginTop + 'px');
            if (borderStyle) {
                style.borderStyle = borderStyle;
                if (borderWidth) {
                    style.borderWidth = borderWidth + 'px';
                }
                if (borderColor) {
                    style.borderColor = borderColor;
                }
                if (borderRadius) {
                    style.borderRadius = borderRadius;
                }
            } else {
                if (topBorderStyle) {
                    style.borderTopStyle = topBorderStyle;
                    if (topBorderWidth) {
                        style.borderTopWidth = topBorderWidth + 'px';
                    }
                    if (topBorderColor) {
                        style.borderTopColor = topBorderColor;
                    }
                    if (topBorderRadius) {
                        style.borderTopLeftRadius = topBorderRadius;
                    }
                }
                if (bottomBorderStyle) {
                    style.borderBottomStyle = bottomBorderStyle;
                    if (bottomBorderWidth) {
                        style.borderBottomWidth = bottomBorderWidth + 'px';
                    }
                    if (bottomBorderColor) {
                        style.borderBottomColor = bottomBorderColor;
                    }
                    if (bottomBorderRadius) {
                        style.borderBottomRightRadius = bottomBorderRadius;
                    }
                }
                if (rightBorderStyle) {
                    style.borderRightStyle = rightBorderStyle;
                    if (rightBorderWidth) {
                        style.borderRightWidth = rightBorderWidth + 'px';
                    }
                    if (rightBorderColor) {
                        style.borderRightColor = rightBorderColor;
                    }
                    if (rightBorderRadius) {
                        style.borderTopRightRadius = rightBorderRadius;
                    }
                }
                if (leftBorderStyle) {
                    style.borderLeftStyle = leftBorderStyle;
                    if (leftBorderWidth) {
                        style.borderLeftWidth = leftBorderWidth + 'px';
                    }
                    if (leftBorderColor) {
                        style.borderLeftColor = leftBorderColor;
                    }
                    if (leftBorderRadius) {
                        style.borderBottomLeftRadius = leftBorderRadius;
                    }
                }
            }
            return (
                <ElementTag id={elementID} className={classes} style={style}>
                    <InnerBlocks.Content/>
                </ElementTag>
            );
        },
    });
})(wp.i18n, wp.blocks, wp.editor, wp.components, wp.element);

function opacityRatioToClass(ratio) {
    return (0 === ratio) ?
        null :
        'has-background-opacity-' + (10 * Math.round(ratio / 10));
}
