// Set up `wp.*` aliases.  Doing this because any tests importing wp stuff will likely run into this.
global.wp = {};

// eljeSettings is required by @elje/* packages
global.eljeSettings = {
	adminUrl: 'https://vagrant.local/wp/wp-admin/',
	currentUserIsAdmin: false,
	date: {
		dow: 0,
	},
	placeholderImgSrc: 'placeholder.jpg',
	postCount: 101,
	locale: {
		siteLocale: 'en_US',
		userLocale: 'en_US',
		weekdaysShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ],
	},
};

global.jQuery = () => ( {
	on: () => void null,
	off: () => void null,
} );

global.IntersectionObserver = function () {
	return {
		observe: () => void null,
		unobserve: () => void null,
	};
};

global.__webpack_public_path__ = '';
