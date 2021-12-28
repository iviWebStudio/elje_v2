import {
	useCollection,
	useCollectionHeader,
} from './collections';

/**
 * This is a custom hook that is wired up to the `elje/store/collections` data
 * store for the `wp/v2/posts` route. Given a query object, this
 * will ensure a component is kept up to date with the posts matching that
 * query in the store state.
 *
 * @param {Object} query   An object containing any query arguments to be
 *                         included with the collection request for the
 *                         posts. Does not have to be included.
 *
 * @return {Object} This hook will return an object with three properties:
 *                  - posts        An array of post objects.
 *                  - totalPosts   The total number of posts that match
 *                                    the given query parameters.
 *                  - postsLoading A boolean indicating whether the posts
 *                                    are still loading or not.
 */
export const useStorePosts = (query) => {
	const collectionOptions = {
		namespace: '/wp/v2/',
		resourceName: 'posts',
	};
	const {results: posts, isLoading: postsLoading} = useCollection({
		...collectionOptions,
		query,
	});
	const {value: totalPosts} = useCollectionHeader('x-wp-total', {
		...collectionOptions,
		query,
	});
	return {
		posts,
		totalPosts: parseInt(totalPosts, 10),
		postsLoading,
	};
};
