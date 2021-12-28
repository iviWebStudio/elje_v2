import PropTypes
	from 'prop-types';
import {SnackbarList} from 'wordpress-components';
import classnames
	from 'classnames';
import {__experimentalApplyCheckoutFilter} from '@elje/blocks-checkout';

const EMPTY_SNACKBAR_NOTICES = {};

const SnackbarNoticesContainer = ({
									  className,
									  notices,
									  removeNotice,
									  isEditor,
								  }) => {
	if (isEditor) {
		return null;
	}

	const snackbarNotices = notices.filter(
		(notice) => notice.type==='snackbar',
	);

	const noticeVisibility =
		snackbarNotices.length > 0
			? snackbarNotices.reduce((acc, {content}) => {
				acc[content] = true;
				return acc;
			}, {})
			:EMPTY_SNACKBAR_NOTICES;

	const filteredNotices = __experimentalApplyCheckoutFilter({
		filterName: 'snackbarNoticeVisibility',
		defaultValue: noticeVisibility,
	});

	const visibleNotices = snackbarNotices.filter(
		(notice) => filteredNotices[notice.content]===true,
	);

	const wrapperClass = classnames(
		className,
		'elje-block-components-notices__snackbar',
	);

	return (
		<SnackbarList
			notices={ visibleNotices }
			className={ wrapperClass }
			onRemove={ removeNotice }
		/>
	);
};

SnackbarNoticesContainer.propTypes = {
	className: PropTypes.string,
	notices: PropTypes.any,
	removeNotice: PropTypes.any,
	isEditor: PropTypes.bool,
};

export default SnackbarNoticesContainer;
