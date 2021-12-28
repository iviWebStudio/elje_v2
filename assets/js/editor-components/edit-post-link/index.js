import {__} from '@wordpress/i18n';
import {
	external,
	Icon,
} from '@elje/icons';
import {ADMIN_URL} from '@elje/settings';
import {InspectorControls} from '@wordpress/block-editor';
import {usePostDataContext} from '@elje/shared-context';

/**
 * Component to render an edit post link in the sidebar.
 *
 * @param {Object} props Component props.
 */
const EditPostLink = (props) => {
	const postDataContext = usePostDataContext();
	const post = postDataContext.post || {};
	const postId = post.id || props.postId || 0;

	if (!postId) {
		return null;
	}

	return (
		<InspectorControls>
			<div className="elje-block-post-card__edit-card">
				<div className="elje-block-post-card__edit-card-title">
					<a
						href={ `${ ADMIN_URL }post.php?post=${ postId }&action=edit` }
						target="_blank"
						rel="noopener noreferrer"
					>
						{ __(
							'Edit this post\'s details',
							'elje',
						) }
						<Icon srcElement={ external } size={ 16 }/>
					</a>
				</div>
				<div className="elje-block-post-card__edit-card-description">
					{ __(
						'Edit details such as title, content and more.',
						'elje',
					) }
				</div>
			</div>
		</InspectorControls>
	);
};

export default EditPostLink;
