import {lazy} from '@wordpress/element';
import {ELJE_BLOCKS_BUILD_URL} from '@elje/block-settings';
import {registerBlockComponent} from '@elje/blocks-registry';

// Modify webpack publicPath at runtime based on location of WordPress Plugin.
// eslint-disable-next-line no-undef,camelcase
__webpack_public_path__ = ELJE_BLOCKS_BUILD_URL;

registerBlockComponent({
	blockName: 'elje/post-image',
	component: lazy(() =>
		import(
			/* webpackChunkName: "atomic-block-components/image" */ './card-elements/image/frontend'
			),
	),
});

registerBlockComponent({
	blockName: 'elje/post-title',
	component: lazy(() =>
		import(
			/* webpackChunkName: "atomic-block-components/title" */ './card-elements/title/frontend'
			),
	),
});

registerBlockComponent({
	blockName: 'elje/post-button',
	component: lazy(() =>
		import(
			/* webpackChunkName: "atomic-block-components/button" */ './card-elements/button/block'
			),
	),
});

registerBlockComponent({
	blockName: 'elje/post-summary',
	component: lazy(() =>
		import(
			/* webpackChunkName: "atomic-block-components/summary" */ './card-elements/summary/block'
			),
	),
});

registerBlockComponent({
	blockName: 'elje/post-category-list',
	component: lazy(() =>
		import(
			/* webpackChunkName: "atomic-block-components/category-list" */ './card-elements/category-list/block'
			),
	),
});

registerBlockComponent({
	blockName: 'elje/post-tag-list',
	component: lazy(() =>
		import(
			/* webpackChunkName: "atomic-block-components/tag-list" */ './card-elements/tag-list/block'
			),
	),
});
