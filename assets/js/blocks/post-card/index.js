import React from 'react';
import './editor.scss';
import './style.scss';

import PrimaryPostCard
	from './template-primary';
import {attributes} from './includes';
import SecondaryPostCard
	from './template-secondary';

import {__} from '@wordpress/i18n';
import {registerBlockType} from '@wordpress/blocks';
import {
	TextControl,
	Panel,
	PanelBody,
	PanelRow,
} from '@wordpress/components';
import {
	ColorPalette,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	FontSizePicker,
	__experimentalBoxControl,
	__experimentalUnitControl,
	useBlockProps,
} from '@wordpress/block-editor';
import {ELJE_ASSET_URL} from '@elje/settings';

const BoxControl = __experimentalBoxControl;
const UnitControl = __experimentalUnitControl;


registerBlockType('elje/post-card', {
	apiVersion: 2,
	title: __('Post card', 'elje'),
	attributes,
	category: 'elje-blocks',
	description: __('The post card markup with 2 layouts, with tile/image/content/button elements.', 'elje'),
	example: attributes,
	edit: ({attributes, setAttributes}) => {
		const ALLOWED_MEDIA_TYPES = ['image'];
		const cardProps = useBlockProps({
			style: {
				width: attributes.width,
				height: attributes.height,
			},
		});

		return (
			<React.Fragment>
				<InspectorControls key="setting">
					<Panel className="elje-block-admin">
						<PanelBody title={ __('Layout', 'elje') }>
							<PanelRow className="d-block">
								<div className="row">
									<div className="col-lg-6">
										<div className={ attributes.activeLayout==='primary' ? 'elje-post-card-layout active':'elje-post-card-layout' } onClick={ () => setAttributes({activeLayout: 'primary'}) }>
											<img src={ getImage('card-layout.png') } alt="card layout primary"/>
										</div>
									</div>
									<div className="col-lg-6">
										<div className={ attributes.activeLayout==='secondary' ? 'elje-post-card-layout active':'elje-post-card-layout' } onClick={ () => setAttributes({activeLayout: 'secondary'}) }>
											<img src={ getImage('card-layout.png') } alt='card layout primary'/>
										</div>
									</div>
								</div>
							</PanelRow>
							<PanelRow className="d-block">
								<div className="row">
									<div className="col-6">
										<UnitControl
											label="Width"
											value={ attributes.width }
											onChange={ value => {
												setAttributes({width: value});
											} }
										/>
									</div>
									<div className="col-6">
										<UnitControl
											label="Height"
											value={ attributes.height }
											onChange={ value => {
												setAttributes({height: value});
											} }
										/>
									</div>
								</div>
							</PanelRow>
							<PanelRow className="d-block">
								<div>
									<label className="components-custom-select-control__label">
										{ __('Header Text', 'elje') }
									</label>
									<TextControl
										value={ attributes.headerText }
										onChange={ text => {
											setAttributes({headerText: text});
										} }
									/>
								</div>
								<div>
									<MediaUploadCheck>
										<MediaUpload
											onSelect={ (media) =>
												setAttributes({imageId: media.url})
											}
											allowedTypes={ ALLOWED_MEDIA_TYPES }
											value={ attributes.imageId }
											render={ ({open}) => (
												<div className="add-image-wrapper" onClick={ open }>
													{ attributes.imageId ?
														<img src={ attributes.imageId } alt="open"/>:
														<span>{ __('Add image', 'elje') }</span> }
												</div>
											) }
										/>
									</MediaUploadCheck>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Body Text', 'elje') }
									</label>
									<TextControl
										value={ attributes.bodyText }
										onChange={ text => {
											setAttributes({bodyText: text});
										} }
									/>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Button text', 'elje') }
									</label>
									<TextControl
										value={ attributes.buttonText }
										onChange={ value => {
											setAttributes({buttonText: value});
										} }
									/>
									<label className="components-custom-select-control__label">
										{ __('Button link', 'elje') }
									</label>
									<TextControl
										value={ attributes.buttonLink }
										onChange={ value => {
											setAttributes({buttonLink: value});
										} }
									/>
								</div>
							</PanelRow>
						</PanelBody>
						<PanelBody title={ __('Colors', 'elje') } initialOpen={ false }>
							<PanelRow className="d-block">
								<div>
									<label className="components-custom-select-control__label">
										{ __('Header Background color', 'elje') }
									</label>
									<ColorPalette
										value={ attributes.headerBgColor }
										onChange={ hexColor => {
											setAttributes({headerBgColor: hexColor});
										} }
									/>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Header Text color', 'elje') }
									</label>
									<ColorPalette
										value={ attributes.headerTextColor }
										onChange={ hexColor => {
											setAttributes({headerTextColor: hexColor});
										} }
									/>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Body Background color', 'elje') }
									</label>
									<ColorPalette
										value={ attributes.bodyBgColor }
										onChange={ hexColor => {
											setAttributes({bodyBgColor: hexColor});
										} }
									/>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Body Text color', 'elje') }
									</label>
									<ColorPalette
										value={ attributes.bodyTextColor }
										onChange={ hexColor => {
											setAttributes({bodyTextColor: hexColor});
										} }
									/>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Button Background color', 'elje') }
									</label>
									<ColorPalette
										value={ attributes.buttonBgColor }
										onChange={ hexColor => {
											setAttributes({buttonBgColor: hexColor});
										} }
									/>
								</div>
								<div>
									<label className="components-custom-select-control__label">
										{ __('Button Text color', 'elje') }
									</label>
									<ColorPalette
										value={ attributes.buttonTextColor }
										onChange={ hexColor => {
											setAttributes({buttonTextColor: hexColor});
										} }
									/>
								</div>
							</PanelRow>
						</PanelBody>
						<PanelBody title={ __('Typography', 'elje') } initialOpen={ false }>
							<PanelRow className="d-block">
								<fieldset>
									<legend>{ __('Header', 'elje') }</legend>
									<FontSizePicker
										value={ attributes.headerFontSize }
										disableCustomFontSizes
										onChange={ size => {
											setAttributes({headerFontSize: size});
										} }
									/>
								</fieldset>
								<fieldset>
									<legend>{ __('Body', 'elje') }</legend>
									<FontSizePicker
										value={ attributes.bodyFontSize }
										disableCustomFontSizes
										onChange={ size => {
											setAttributes({bodyFontSize: size});
										} }
									/>
								</fieldset>
								<fieldset>
									<legend>{ __('Button', 'elje') }</legend>
									<FontSizePicker
										value={ attributes.buttonFontSize }
										disableCustomFontSizes
										onChange={ size => {
											setAttributes({buttonFontSize: size});
										} }
									/>
								</fieldset>
							</PanelRow>
						</PanelBody>
						<PanelBody title={ __('Margins', 'elje') } initialOpen={ false }>
							<BoxControl
								label={ __('Header padding', 'elje') }
								values={ attributes.headerPadding }
								units={ mainUnits }
								splitOnAxis={ true }
								onChange={ el => {
									setAttributes({headerPadding: el});
								} }
							/>
							<BoxControl
								label={ __('Header margin', 'elje') }
								inputProps={ {min: -50} }
								values={ attributes.headerMargin }
								units={ mainUnits }
								splitOnAxis={ true }
								onChange={ el => {
									setAttributes({headerMargin: el});
								} }
							/>
							<BoxControl
								label={ __('Body padding', 'elje') }
								values={ attributes.bodyPadding }
								units={ mainUnits }
								splitOnAxis={ true }
								onChange={ el => {
									setAttributes({bodyPadding: el});
								} }
							/>
							<BoxControl
								label={ __('Body margin', 'elje') }
								inputProps={ {min: -50} }
								values={ attributes.bodyMargin }
								units={ mainUnits }
								splitOnAxis={ true }
								onChange={ el => {
									setAttributes({bodyMargin: el});
								} }
							/>
							<BoxControl
								label={ __('Button padding', 'elje') }
								values={ attributes.buttonPadding }
								units={ mainUnits }
								splitOnAxis={ true }
								onChange={ el => {
									setAttributes({buttonPadding: el});
								} }
							/>
							<BoxControl
								label={ __('Button margin', 'elje') }
								inputProps={ {min: -50} }
								values={ attributes.buttonMargin }
								units={ mainUnits }
								splitOnAxis={ true }
								onChange={ el => {
									setAttributes({buttonMargin: el});
								} }
							/>
						</PanelBody>
					</Panel>
				</InspectorControls>
				<div { ...cardProps }>
					{
						attributes.activeLayout==='primary' ?
							<PrimaryPostCard attributes={ attributes }/>:
							<SecondaryPostCard attributes={ attributes }/>
					}
				</div>
			</React.Fragment>
		);
	},
	save: ({attributes}) => {
		const cardProps = useBlockProps.save({
			className: `elje-block elje-card layout-${ attributes.activeLayout }`,
			style: {
				width: '18rem',
			},
		});
		return (
			attributes.activeLayout==='primary' ?
				<PrimaryPostCard attributes={ attributes } cardProps={ cardProps }/>:
				<SecondaryPostCard attributes={ attributes } cardProps={ cardProps }/>
		);
	},
});

export const checkURL = str => {
	const pattern = new RegExp('^(https?:\\/\\/)?' +
		'((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' +
		'((\\d{1,3}\\.){3}\\d{1,3}))' +
		'(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' +
		'(\\?[;&a-z\\d%_.~+=-]*)?' +
		'(\\#[-a-z\\d_]*)?$', 'i');
	return !!pattern.test(str);
};

const getImage = imagePath => {
	return ELJE_ASSET_URL + '/images/' + imagePath;
};


const mainUnits = [
	{
		'value': 'px',
		'label': 'px',
	},
	{
		'value': '%',
		'label': '%',
	},
	{
		'value': 'rem',
		'label': 'rem',
	},
]
