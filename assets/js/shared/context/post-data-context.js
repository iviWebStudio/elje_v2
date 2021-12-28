/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { createContext, useContext } from '@wordpress/element';

/**
 * Default post shape matching API response.
 */
const defaultPostData = {
	id: 0,
	name: '',
	parent: 0,
	permalink: '',
	excerpt: '',
	content: '',
	thumbnail: {},
	categories: [],
	tags: [],
};

/**
 * This context is used to pass post data down to all children blocks in a given tree.
 *
 * @member {Object} PostDataContext A react context object
 */
const PostDataContext = createContext( {
	post: defaultPostData,
	hasContext: false,
} );

export const usePostDataContext = () => useContext( PostDataContext );

export const PostDataContextProvider = ( {
												post = null,
												children,
												isLoading = false,
											} ) => {
	const contextValue = {
		post: post || defaultPostData,
		hasContext: true,
	};

	return (
		<PostDataContext.Provider value={ contextValue }>
			{ isLoading ? (
				<div className="is-loading">{ children }</div>
			) : (
				children
			) }
		</PostDataContext.Provider>
	);
};

PostDataContextProvider.propTypes = {
	children: PropTypes.node,
	post: PropTypes.object,
};
