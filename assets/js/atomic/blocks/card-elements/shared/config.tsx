import {__} from '@wordpress/i18n';
import {
	grid,
	Icon
} from '@elje/icons';
import {isExperimentalBuild} from '@elje/block-settings';
import type {BlockConfiguration} from '@wordpress/blocks';

import save
	from '../save';

/**
 * Holds default config for this collection of blocks.
 * attributes and title are omitted here as these are added on an individual block level.
 */
const sharedConfig: Omit<BlockConfiguration, 'attributes' | 'title'> = {
	category: 'elje-card-elements',
	keywords: [__('Elje', 'elje')],
	icon: {
		src: (
			<Icon
				srcElement={grid}
				className="elje-block-editor-components-block-icon"
			/>
		),
	},
	supports: {
		html: false,
	},
	parent: isExperimentalBuild()
		? undefined
		: ['@elje/post-listings', '@elje/post-card'],
	save,
	deprecated: [
		{
			attributes: {},
			save(): null {
				return null;
			},
		},
	],
};

export default sharedConfig;
