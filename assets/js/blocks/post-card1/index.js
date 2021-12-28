/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
import edit
	from './edit';
import save
	from './save';
import attributes
	from './attributes';
import {
	BLOCK_DESCRIPTION,
	BLOCK_ICON,
	BLOCK_NAME,
	BLOCK_TITLE,
} from './constants';
import {registerBlockType} from '@wordpress/blocks';

const settings = {
	title: BLOCK_TITLE,
	icon: {
		src: BLOCK_ICON,
	},
	category: 'elje',
	keywords: ['elje'],
	description: BLOCK_DESCRIPTION,
	supports: {
		align: ['wide', 'full'],
		html: false,
	},
	example: {
		attributes: {
			isPreview: true,
		},
	},
	attributes,
	edit,
	save,
};

registerBlockType(BLOCK_NAME, settings);
