import { registerBlockType } from '@wordpress/blocks';

import sharedConfig from '../shared/config';
import attributes from './attributes';
import edit from './edit';
import {
	BLOCK_TITLE as title,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
} from './constants';

const blockConfig = {
	title,
	description,
	icon: { src: icon },
	attributes,
	edit,
};

registerBlockType( 'elje/post-button', {
	...sharedConfig,
	...blockConfig,
} );
