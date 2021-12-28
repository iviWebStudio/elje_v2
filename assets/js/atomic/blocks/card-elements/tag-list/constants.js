import {__} from '@wordpress/i18n';
import {
	Icon,
	tag,
} from '@elje/icons';

export const BLOCK_TITLE = __(
	'Post Tag List',
	'elje',
);
export const BLOCK_ICON = (
	<Icon
		srcElement={ tag }
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION = __(
	'Display a list of tags belonging to a post.',
	'elje',
);
