import { getCategories, setCategories } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import '../css/editor.scss';
import '../css/style.scss';
import './filters/block-list-block';

setCategories( [
	...getCategories().filter(
		( { slug } ) =>
			slug !== 'elje'
	),
	{
		slug: 'elje',
		title: __( 'ELJE', 'elje' ),
		//icon: <Icon srcElement={ elje } />,
	}
] );
