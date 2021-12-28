import {__} from '@wordpress/i18n';
import {
	folder,
	Icon
} from '@elje/icons';

export const BLOCK_TITLE: string = __(
	'Post Category List',
	'elje'
);
export const BLOCK_ICON: JSX.Element = (
	<Icon
		srcElement={folder}
		className="elje-block-editor-components-block-icon"
	/>
);
export const BLOCK_DESCRIPTION: string = __(
	'Display a list of categories belonging to a post.',
	'elje'
);
