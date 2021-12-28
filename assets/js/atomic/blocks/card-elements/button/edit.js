import { __ } from '@wordpress/i18n';
import { Disabled } from '@wordpress/components';

import Block from './block';
import withPostSelector from '../shared/with-post-selector';
import { BLOCK_TITLE, BLOCK_ICON } from './constants';

const Edit = ( { attributes } ) => {
	return (
		<Disabled>
			<Block { ...attributes } />
		</Disabled>
	);
};

export default withPostSelector( {
	icon: BLOCK_ICON,
	label: BLOCK_TITLE,
	description: __(
		'Choose a post to display its add to cart button.',
		'elje'
	),
} )( Edit );
