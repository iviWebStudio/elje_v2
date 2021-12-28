import {__} from '@wordpress/i18n';
import {
	cart,
	Icon,
} from '@elje/icons';

export const BLOCK_TITLE = __(
	'Button element',
	'elje',
);
export const BLOCK_ICON = (
	<Icon
		srcElement={ cart }
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION = __(
	'Display a cta button.',
	'elje',
);
