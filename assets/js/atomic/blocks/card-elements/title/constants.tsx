import {__} from '@wordpress/i18n';
import {
	bookmark,
	Icon
} from '@elje/icons';

export const BLOCK_TITLE: string = __(
	'Post Title',
	'elje'
);
export const BLOCK_ICON: JSX.Element = (
	<Icon
		srcElement={bookmark}
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION: string = __(
	'Display the title of a post.',
	'elje'
);
