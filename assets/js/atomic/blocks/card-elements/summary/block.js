import PropTypes
	from 'prop-types';
import classnames
	from 'classnames';
import Summary
	from '@elje/base-components/summary';
import {blocksConfig} from '@elje/block-settings';
import {
	useInnerBlockLayoutContext,
	usePostDataContext,
} from '@elje/shared-context';
import {withPostDataContext} from '@elje/shared-hocs';

import './style.scss';

/**
 * Post Summary Block Component.
 *
 * @param {Object} props             Incoming props.
 * @param {string} [props.className] CSS Class name for the component.
 * @return {*} The component.
 */
const Block = ({className}) => {
	const {parentClassName} = useInnerBlockLayoutContext();
	const {post} = usePostDataContext();

	if (!post) {
		return (
			<div
				className={ classnames(
					className,
					`elje-block-components-post-summary`,
					{
						[`${ parentClassName }__post-summary`]: parentClassName,
					},
				) }
			/>
		);
	}

	const source = post.excerpt
		? post.excerpt
		:post.content;

	if (!source) {
		return null;
	}

	return (
		<Summary
			className={ classnames(
				className,
				`elje-block-components-post-summary`,
				{
					[`${ parentClassName }__post-summary`]: parentClassName,
				},
			) }
			source={ source }
			maxLength={ 150 }
			countType={ blocksConfig.wordCountType || 'words' }
		/>
	);
};

Block.propTypes = {
	className: PropTypes.string,
};

export default withPostDataContext(Block);
