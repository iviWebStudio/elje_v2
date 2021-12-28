import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {getPost} from '@elje/editor-components/utils';

import {formatError} from '../base/utils/errors.js';

/**
 * HOC that queries a post for a component.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
const withPost = createHigherOrderComponent((OriginalComponent) => {
	return class WrappedComponent extends Component {
		state = {
			error: null,
			loading: false,
			post:
				this.props.attributes.postId==='preview'
					? this.props.attributes.previewPost
					:null,
		};

		componentDidMount() {
			this.loadPost();
		}

		componentDidUpdate(prevProps) {
			if (
				prevProps.attributes.postId!==
				this.props.attributes.postId
			) {
				this.loadPost();
			}
		}

		loadPost = () => {
			const {postId} = this.props.attributes;

			if (postId==='preview') {
				return;
			}

			if (!postId) {
				this.setState({
					post: null,
					loading: false,
					error: null,
				});
				return;
			}

			this.setState({loading: true});

			getPost(postId).then((post) => {
				this.setState({
					post,
					loading: false,
					error: null,
				});
			}).catch(async (e) => {
				const error = await formatError(e);

				this.setState({
					post: null,
					loading: false,
					error,
				});
			});
		};

		render() {
			const {error, loading, post} = this.state;

			return (
				<OriginalComponent
					{ ...this.props }
					error={ error }
					getPost={ this.loadPost }
					isLoading={ loading }
					post={ post }
				/>
			);
		}
	};
}, 'withPost');

export default withPost;
