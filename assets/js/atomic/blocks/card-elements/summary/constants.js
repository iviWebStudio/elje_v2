import {__} from '@wordpress/i18n';
import {
	Icon,
	notes,
} from '@elje/icons';

export const BLOCK_TITLE = __(
	'Post Summary',
	'elje',
);
export const BLOCK_ICON = (
	<Icon
		srcElement={ notes }
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION = __(
	'Display a short description about a post.',
	'elje',
);
