import {__} from '@wordpress/i18n';
import {
	Icon,
	image,
} from '@elje/icons';

export const BLOCK_TITLE = __(
	'Post card Image',
	'elje',
);
export const BLOCK_ICON = (
	<Icon
		srcElement={ image }
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION = __(
	'Display the post image',
	'elje',
);
