/* eslint-disable you-dont-need-lodash-underscore/flatten -- until we have an alternative to uniqBy we'll keep flatten to avoid potential introduced bugs with alternatives */
import {addQueryArgs} from '@wordpress/url';
import apiFetch
	from '@wordpress/api-fetch';
import {
	flatten,
	uniqBy,
} from 'lodash';
import {getSetting} from '@elje/settings';
import {blocksConfig} from '@elje/block-settings';

/**
 * Get post query requests for the Store API.
 *
 * @param {Object} request A query object with the list of selected posts and search term.
 * @param {number[]} request.selected Currently selected posts.
 * @param {string=} request.search Search string.
 * @param {(Record<string, unknown>)=} request.queryArgs Query args to pass in.
 */
const getPostsRequests = ({
							  selected = [],
							  search = '',
							  queryArgs = {},
						  }) => {
	const isLargeCatalog = blocksConfig.postCount > 100;
	const defaultArgs = {
		per_page: isLargeCatalog ? 100:0,
		search,
		orderby: 'title',
		order: 'asc',
	};
	const requests = [
		addQueryArgs('/wp/v2/posts', {...defaultArgs, ...queryArgs}),
	];

	// If we have a large catalog, we might not get all selected posts in the first page.
	if (isLargeCatalog && selected.length) {
		requests.push(
			addQueryArgs('/wp/v2/posts', {
				include: selected,
				per_page: 0,
			}),
		);
	}

	return requests;
};

/**
 * Get a promise that resolves to a list of posts from the Store API.
 *
 * @param {Object} request A query object with the list of selected posts and search term.
 * @param {number[]} request.selected Currently selected posts.
 * @param {string=} request.search Search string.
 * @param {(Record<string, unknown>)=} request.queryArgs Query args to pass in.
 * @return {Promise<unknown>} Promise resolving to a Post list.
 * @throws Exception if there is an error.
 */
export const getPosts = ({
							 selected = [],
							 search = '',
							 queryArgs = {},
						 }) => {
	const requests = getPostsRequests({
		selected,
		search,
		queryArgs,
	});

	return Promise.all(requests.map((path) => apiFetch({path}))).then((data) => {
		const posts = uniqBy(flatten(data), 'id');
		const list = posts.map((post) => ({
			...post,
			parent: 0,
		}));
		return list;
	}).catch((e) => {
		throw e;
	});
};

/**
 * Get a promise that resolves to a post object from the Store API.
 *
 * @param {number} postId Id of the post to retrieve.
 */
export const getPost = (postId) => {
	return apiFetch({
		path: `/elje/store/posts/${ postId }`,
	});
};

/**
 * Get post tag query requests for the Store API.
 *
 * @param {Object} request A query object with the list of selected posts and search term.
 * @param {Array} request.selected Currently selected tags.
 * @param {string} request.search Search string.
 */
const getPostTagsRequests = ({selected = [], search}) => {
	const limitTags = getSetting('limitTags', false);
	const requests = [
		addQueryArgs(`/wp/v2/tags`, {
			per_page: limitTags ? 100:0,
			orderby: limitTags ? 'count':'name',
			order: limitTags ? 'desc':'asc',
			search,
		}),
	];

	// If we have a large catalog, we might not get all selected posts in the first page.
	if (limitTags && selected.length) {
		requests.push(
			addQueryArgs(`/wp/v2/tags`, {
				include: selected,
			}),
		);
	}

	return requests;
};

/**
 * Get a promise that resolves to a list of tags from the Store API.
 *
 * @param {Object} props A query object with the list of selected posts and search term.
 * @param {Array} props.selected
 * @param {string} props.search
 */
export const getPostTags = ({selected = [], search}) => {
	const requests = getPostTagsRequests({
		selected,
		search,
	});

	return Promise.all(requests.map((path) => apiFetch({path}))).then(
		(data) => {
			return uniqBy(flatten(data), 'id');
		},
	);
};

/**
 * Get a promise that resolves to a list of category objects from the Store API.
 *
 * @param {Object} queryArgs Query args to pass in.
 */
export const getCategories = (queryArgs) => {
	return apiFetch({
		path: addQueryArgs(`/wp/v2/categories`, {
			per_page: 0,
			...queryArgs,
		}),
	});
};

/**
 * Get a promise that resolves to a category object from the API.
 *
 * @param {number} categoryId Id of the post to retrieve.
 */
export const getCategory = (categoryId) => {
	return apiFetch({
		path: `/wp/v2/categories/${ categoryId }`,
	});
};

/**
 * Given a page object and an array of page, format the title.
 *
 * @param  {Object} page           Page object.
 * @param  {Object} page.title     Page title object.
 * @param  {string} page.title.raw Page title.
 * @param  {string} page.slug      Page slug.
 * @param  {Array}  pages          Array of all pages.
 * @return {string}                Formatted page title to display.
 */
export const formatTitle = (page, pages) => {
	if (!page.title.raw) {
		return page.slug;
	}
	const isUnique =
		pages.filter((p) => p.title.raw===page.title.raw).length===1;
	return page.title.raw + (!isUnique ? ` - ${ page.slug }`:'');
};
