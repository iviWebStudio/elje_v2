import PropTypes
	from 'prop-types';
import classnames
	from 'classnames';
import {
	useInnerBlockLayoutContext,
	usePostDataContext,
} from '@elje/shared-context';
import {withPostDataContext} from '@elje/shared-hocs';

import './style.scss';

/**
 * Card Button Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {string} [props.className] CSS Class name for the component.
 * @return {*} The component.
 */
const Block = ({className}) => {
	const {parentClassName} = useInnerBlockLayoutContext();
	const {post} = usePostDataContext();

	return (
		<div
			className={ classnames(
				className,
				'wp-block-button',
				'elje-block-components-post-button',
				{
					[`${ parentClassName }__post-add-to-cart`]: parentClassName,
				},
			) }
		>
			<a
				className={ classnames(
					'wp-block-button__link',
					'add_to_cart_button',
					'elje-block-components-post-button__button',
					'elje-block-components-post-button__button--placeholder',
				) }
				href={ post.permalink }
			>{ post.name }</a>
		</div>
	);
};


Block.propTypes = {
	className: PropTypes.string,
};

export default withPostDataContext(Block);
