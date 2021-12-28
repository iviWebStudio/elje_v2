import PropTypes
	from 'prop-types';
import classnames
	from 'classnames';
import {Notice} from 'wordpress-components';

import './style.scss';

const getEljeClassName = ({status = 'default'}) => {
	switch (status) {
		case 'error':
			return 'elje-error';
		case 'success':
			return 'elje-message';
		case 'info':
		case 'warning':
			return 'elje-info';
	}
	return '';
};

const StoreNoticesContainer = ({className, notices, removeNotice}) => {
	const regularNotices = notices.filter(
		(notice) => notice.type!=='snackbar',
	);

	if (!regularNotices.length) {
		return null;
	}

	const wrapperClass = classnames(className, 'elje-block-components-notices');

	return (
		<div className={ wrapperClass }>
			{ regularNotices.map((props) => (
				<Notice
					key={ 'store-notice-' + props.id }
					{ ...props }
					className={ classnames(
						'elje-block-components-notices__notice',
						getEljeClassName(props),
					) }
					onRemove={ () => {
						if (props.isDismissible) {
							removeNotice(props.id);
						}
					} }
				>
					{ props.content }
				</Notice>
			)) }
		</div>
	);
};

StoreNoticesContainer.propTypes = {
	className: PropTypes.string,
	notices: PropTypes.arrayOf(
		PropTypes.shape({
			content: PropTypes.string.isRequired,
			id: PropTypes.string.isRequired,
			status: PropTypes.string.isRequired,
			isDismissible: PropTypes.bool,
			type: PropTypes.oneOf(['default', 'snackbar']),
		}),
	),
};

export default StoreNoticesContainer;
