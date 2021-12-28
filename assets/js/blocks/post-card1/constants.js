/**
 * External dependencies
 */
import {__} from '@wordpress/i18n';
import {
	Icon,
	reader,
} from '@elje/icons';
import {getBlockMap} from '@elje/atomic-utils';

export const BLOCK_NAME = 'elje/post-card';
export const BLOCK_TITLE = __(
	'Post card',
	'elje',
);
export const BLOCK_ICON = (
	<Icon
		srcElement={ reader }
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION = __(
	'Display a single post.',
	'elje',
);

export const DEFAULT_INNER_BLOCKS = [
	[
		'core/columns',
		{},
		[
			[
				'core/column',
				{},
				[['elje/post-image']],
			],
			[
				'core/column',
				{},
				[
					['elje/post-title', {headingLevel: 2}],
					['elje/post-summary'],
					['elje/post-category-list'],
					['elje/post-tag-list'],
				],
			],
		],
	],
];

export const ALLOWED_INNER_BLOCKS = [
	'core/columns',
	'core/column',
	...Object.keys(getBlockMap(BLOCK_NAME)),
];
