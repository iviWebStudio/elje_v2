import {registerBlockType} from '@wordpress/blocks';

import sharedConfig
	from '../shared/config';
import attributes
	from './attributes';
import edit
	from './edit';
import {
	BLOCK_DESCRIPTION as description,
	BLOCK_ICON as icon,
	BLOCK_TITLE as title,
} from './constants';

const blockConfig = {
	title,
	description,
	icon: {src: icon},
	attributes,
	edit,
};

registerBlockType('elje/post-image', {
	...sharedConfig,
	...blockConfig,
});
