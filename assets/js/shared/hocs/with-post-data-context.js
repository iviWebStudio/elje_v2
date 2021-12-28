import apiFetch from '@wordpress/api-fetch';
import {
	PostDataContextProvider,
	usePostDataContext,
} from '@elje/shared-context';
import { useState, useEffect } from '@wordpress/element';

/**
 * Loads the post from the API and adds to the context provider.
 *
 * @param {Object} props Component props.
 */
const OriginalComponentWithContext = ( props ) => {
	const { postId, OriginalComponent } = props;
	const [ post, setPost ] = useState( null );
	const [ isLoading, setIsLoading ] = useState( true );

	useEffect( () => {
		if ( !! props.post ) {
			setPost( props.post );
			setIsLoading( false );
		}
	}, [ props.post ] );

	useEffect( () => {
		if ( postId > 0 ) {
			setIsLoading( true );
			apiFetch( {
				path: `/wp/v2/posts/${ postId }`,
			} )
				.then( ( receivedPost ) => {
					setPost( receivedPost );
				} )
				.catch( async () => {
					setPost( null );
				} )
				.finally( () => {
					setIsLoading( false );
				} );
		}
	}, [ postId ] );

	if ( ! isLoading && ! post ) {
		return null;
	}

	return (
		<PostDataContextProvider post={ post } isLoading={ isLoading }>
			<OriginalComponent { ...props } />
		</PostDataContextProvider>
	);
};

/**
 * This HOC sees if the Block is wrapped in Post Data Context, and if not, wraps it with context
 * based on the postId attribute, if set.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
export const withPostDataContext = ( OriginalComponent ) => {
	return ( props ) => {
		const postDataContext = usePostDataContext();

		// If a post prop was provided, use this as the context for the tree.
		if ( !! props.post || ! postDataContext.hasContext ) {
			return (
				<OriginalComponentWithContext
					{ ...props }
					OriginalComponent={ OriginalComponent }
				/>
			);
		}

		return <OriginalComponent { ...props } />;
	};
};
