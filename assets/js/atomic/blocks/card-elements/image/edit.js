import {__} from '@wordpress/i18n';
import {
	Disabled,
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import {InspectorControls} from '@wordpress/block-editor';
import ToggleButtonControl
	from '@elje/editor-components/toggle-button-control';

import Block
	from './block';
import withPostSelector
	from '../shared/with-post-selector';
import {
	BLOCK_ICON,
	BLOCK_TITLE,
} from './constants';

const Edit = ({attributes, setAttributes}) => {
	const {
		showPostLink,
		imageSizing,
	} = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __('Content', 'elje') }
				>
					<ToggleControl
						label={ __(
							'Link to post',
							'elje',
						) }
						help={ __(
							'Links the image to the single post listing.',
							'elje',
						) }
						checked={ showPostLink }
						onChange={ () =>
							setAttributes({
								showPostLink: !showPostLink,
							})
						}
					/>
					<ToggleButtonControl
						label={ __(
							'Image Sizing',
							'elje',
						) }
						value={ imageSizing }
						options={ [
							{
								label: __(
									'Full Size',
									'elje',
								),
								value: 'full-size',
							},
							{
								label: __(
									'Cropped',
									'elje',
								),
								value: 'cropped',
							},
						] }
						onChange={ (value) =>
							setAttributes({imageSizing: value})
						}
					/>
				</PanelBody>
			</InspectorControls>
			<Disabled>
				<Block { ...attributes } />
			</Disabled>
		</>
	);
};

export default withPostSelector({
	icon: BLOCK_ICON,
	label: BLOCK_TITLE,
	description: __(
		'Choose a post to display its image.',
		'elje',
	),
})(Edit);
