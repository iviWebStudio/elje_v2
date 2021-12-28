/**
 * Shown when there is an API error getting a post.
 *
 * @param {Object} props Incoming props for the component.
 * @param {string} props.error
 * @param {boolean} props.isLoading
 * @param {function(any):any} props.getPost
 */
const ApiError = ({error, isLoading, getPost}) => (
	<div
		className={ `elje-block-post-card-error ${ isLoading ? 'loading':'' }` }
	>{ error }</div>
);

export default ApiError;
