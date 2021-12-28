import {__} from '@wordpress/i18n';
import {Disabled} from '@wordpress/components';
import EditPostLink
	from '@elje/editor-components/edit-post-link';

import Block
	from './block';
import withPostSelector
	from '../shared/with-post-selector';
import {
	BLOCK_ICON,
	BLOCK_TITLE
} from './constants';
import {Attributes} from './types';

interface Props {
	attributes: Attributes;
}

const Edit = ({attributes}: Props): JSX.Element => {
	return (
		<>
			<EditPostLink/>
			<Disabled>
				<Block {...attributes} />
			</Disabled>
		</>
	);
};

export default withPostSelector({
	icon: BLOCK_ICON,
	label: BLOCK_TITLE,
	description: __(
		'Choose a post to display its categories.',
		'elje'
	),
})(Edit);
