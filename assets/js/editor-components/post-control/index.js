import {__} from '@wordpress/i18n';
import {isEmpty} from 'lodash';
import PropTypes
	from 'prop-types';

import classNames
	from 'classnames';

import './style.scss';

const messages = {
	list: __('Posts', 'elje'),
	noItems: __(
		'Your store doesn\'t have any posts.',
		'elje',
	),
	search: __(
		'Search for a post to display',
		'elje',
	),
	updated: __(
		'Post search results updated.',
		'elje',
	),
};

const PostControl = ({
						 expandedPost,
						 error,
						 instanceId,
						 isCompact,
						 isLoading,
						 onChange,
						 onSearch,
						 posts,
						 renderItem,
					 }) => {
	const renderItemWithVariations = (args) => {
		const {item, search, depth = 0, isSelected, onSelect} = args;
		const variationsCount =
			item.variations && Array.isArray(item.variations)
				? item.variations.length
				:0;
		const classes = classNames(
			'elje-search-post__item',
			'elje-search-list__item',
			`depth-${ depth }`,
			'has-count',
			{
				'is-searching': search.length > 0,
				'is-skip-level': depth===0 && item.parent!==0,
				'is-variable': variationsCount > 0,
			},
		);

		// Top level items custom rendering based on SearchListItem.
		if (!item.breadcrumbs.length) {
			return (
				<div
					{ ...args }
					className={ classNames(classes, {
						'is-selected': isSelected,
					}) }

					onSelect={ () => {
						return () => {
							onSelect(item)();
						};
					} }
				/>
			);
		}

		const itemArgs = isEmpty(item.variation)
			? args
			:{
				...args,
				item: {
					...args.item,
					name: item.variation,
				},
				'aria-label': `${ item.breadcrumbs[0] }: ${ item.variation }`,
			};

		return (
			<div
				{ ...itemArgs }
				className={ classes }
				data-name={ `card-${ instanceId }` }
			/>
		);
	};

	const getRenderItemFunc = () => {
		if (renderItem) {
			return renderItem;
		}
		return null;
	};

	if (error) {
		return <div className="error">{ error }</div>;
	}


	return (
		<div
			className="elje-posts"
			isCompact={ isCompact }
			isLoading={ isLoading }
			isSingle
			onChange={ onChange }
			renderItem={ getRenderItemFunc() }
			onSearch={ onSearch }
			messages={ messages }
			isHierarchical
		/>
	);
};

PostControl.propTypes = {
	/**
	 * Callback to update the selected posts.
	 */
	onChange: PropTypes.func.isRequired,
	isCompact: PropTypes.bool,
	/**
	 * The ID of the currently expanded post.
	 */
	expandedPost: PropTypes.number,
	/**
	 * Callback to search posts by their name.
	 */
	onSearch: PropTypes.func,
	/**
	 * Query args to pass to getPosts.
	 */
	queryArgs: PropTypes.object,
	/**
	 * Callback to render each item in the selection list, allows any custom object-type rendering.
	 */
	renderItem: PropTypes.func,
	/**
	 * The ID of the currently selected item (post or variation).
	 */
	selected: PropTypes.arrayOf(PropTypes.number),
};

PostControl.defaultProps = {
	isCompact: false,
	expandedPost: null,
	selected: [],
};

