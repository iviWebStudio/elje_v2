import { registerBlockType, BlockConfiguration } from '@wordpress/blocks';
import { isFeaturePluginBuild } from '@elje/block-settings';

import sharedConfig from '../shared/config';
import attributes from './attributes';
import edit from './edit';
import {
	BLOCK_TITLE as title,
	BLOCK_ICON as icon,
	BLOCK_DESCRIPTION as description,
} from './constants';

const blockConfig: BlockConfiguration = {
	...sharedConfig,
	apiVersion: 2,
	title,
	description,
	icon: { src: icon },
	attributes,
	edit,
	supports: isFeaturePluginBuild()
		? {
				html: false,
				color: {
					background: false,
				},
				typography: {
					fontSize: true,
				},
		  }
		: sharedConfig.supports,
};

registerBlockType( 'elje/post-title', blockConfig );
