import {__} from '@wordpress/i18n';
import PropTypes
	from 'prop-types';
import classnames
	from 'classnames';
import {
	useInnerBlockLayoutContext,
	usePostDataContext,
} from '@elje/shared-context';
import {isEmpty} from 'lodash';
import {withPostDataContext} from '@elje/shared-hocs';

import './style.scss';

/**
 * Post Tag List Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {string} [props.className] CSS Class name for the component.
 * @return {*} The component.
 */
const Block = ({className}) => {
	const {parentClassName} = useInnerBlockLayoutContext();
	const {post} = usePostDataContext();

	if (isEmpty(post.tags)) {
		return null;
	}

	return (
		<div
			className={ classnames(
				className,
				'elje-block-components-post-tag-list',
				{
					[`${ parentClassName }__post-tag-list`]: parentClassName,
				},
			) }
		>
			{ __('Tags:', 'elje') }{ ' ' }
			<ul>
				{ Object.values(post.tags).map(
					({name, link, slug}) => {
						return (
							<li key={ `tag-list-item-${ slug }` }>
								<a href={ link }>{ name }</a>
							</li>
						);
					},
				) }
			</ul>
		</div>
	);
};

Block.propTypes = {
	className: PropTypes.string,
};

export default withPostDataContext(Block);
