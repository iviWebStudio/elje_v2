import { allSettings } from './settings-init';

/**
 * This exports all default core settings as constants.
 */
export const ADMIN_URL = allSettings.adminUrl;
export const CURRENT_USER_IS_ADMIN = allSettings.currentUserIsAdmin;
export const HOME_URL = allSettings.homeUrl;
export const LOCALE = allSettings.locale;
export const PLACEHOLDER_IMG_SRC = allSettings.placeholderImgSrc as string;
export const SITE_TITLE = allSettings.siteTitle;
export const ELJE_ASSET_URL = allSettings.eljeAssetUrl;
export const ELJE_VERSION = allSettings.eljeVersion;
export const WP_LOGIN_URL = allSettings.wpLoginUrl;
export const WP_VERSION = allSettings.wpVersion;
