import {registerExperimentalBlockType} from '@elje/block-settings';
import {BlockConfiguration} from '@wordpress/blocks';

import sharedConfig
	from './../shared/config';
import attributes
	from './attributes';
import edit
	from './edit';
import {
	BLOCK_DESCRIPTION as description,
	BLOCK_ICON as icon,
	BLOCK_TITLE as title,
} from './constants';

const blockConfig: BlockConfiguration = {
	...sharedConfig,
	title,
	description,
	icon: {src: icon},
	attributes,
	edit,
};

registerExperimentalBlockType(
	'elje/post-category-list',
	blockConfig
);
