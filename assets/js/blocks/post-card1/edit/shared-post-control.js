/**
 * External dependencies
 */
import PostControl from '@elje/editor-components/post-control';

/**
 * Allows a post to be selected for display.
 *
 * @param {Object} props Incoming props for the component.
 * @param {Object} props.attributes Incoming block attributes.
 * @param {function(any):any} props.setAttributes Setter for block attributes.
 */
const SharedPostControl = ( { attributes, setAttributes } ) => (
	<PostControl
		selected={ attributes.postId || 0 }
		showVariations
		onChange={ ( value = [] ) => {
			const id = value[ 0 ] ? value[ 0 ].id : 0;
			setAttributes( {
				postId: id,
			} );
		} }
	/>
);

export default SharedPostControl;
