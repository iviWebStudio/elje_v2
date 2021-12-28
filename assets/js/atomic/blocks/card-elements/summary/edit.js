import {__} from '@wordpress/i18n';

import Block
	from './block';
import withPostSelector
	from '../shared/with-post-selector';
import {
	BLOCK_ICON,
	BLOCK_TITLE,
} from './constants';

const Edit = ({attributes}) => {
	return <Block { ...attributes } />;
};

export default withPostSelector({
	icon: BLOCK_ICON,
	label: BLOCK_TITLE,
	description: __(
		'Choose a post to display its short description.',
		'elje',
	),
})(Edit);
