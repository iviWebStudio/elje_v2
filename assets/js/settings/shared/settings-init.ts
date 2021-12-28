declare global {
	interface Window {
		eljeSettings: Record< string, unknown >;
	}
}

export interface EljeSiteLocale {
	// The locale string for the current site.
	siteLocale: string;
	// The locale string for the current user.
	userLocale: string;
	// An array of short weekday strings in the current user's locale.
	weekdaysShort: string[];
}

export interface EljeSharedSettings {
	adminUrl: string;
	currentUserIsAdmin: boolean;
	homeUrl: string;
	locale: EljeSiteLocale;
	placeholderImgSrc: string;
	siteTitle: string;
	eljeAssetUrl: string;
	eljeVersion: string;
	wpLoginUrl: string;
	wpVersion: string;
}

const defaults: EljeSharedSettings = {
	adminUrl: '',
	currentUserIsAdmin: false,
	homeUrl: '',
	locale: {
		siteLocale: 'en_US',
		userLocale: 'en_US',
		weekdaysShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ],
	},
	placeholderImgSrc: '',
	siteTitle: '',
	eljeAssetUrl: '',
	eljeVersion: '',
	wpLoginUrl: '',
	wpVersion: '',
};

const globalSharedSettings =
	typeof window.eljeSettings === 'object' ? window.eljeSettings : {};

// Use defaults or global settings, depending on what is set.
const allSettings: Record< string, unknown > = {
	...defaults,
	...globalSharedSettings,
};

allSettings.locale = {
	...defaults.locale,
	...( allSettings.locale as Record< string, unknown > ),
};

export { allSettings };
