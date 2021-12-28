/**
 * @typedef {Object} EljeSiteLocale
 *
 * @property {string}        siteLocale    The locale string for the current
 *                                         site.
 * @property {string}        userLocale    The locale string for the current
 *                                         user.
 * @property {Array<string>} weekdaysShort An array of short weekday strings
 *                                         in the current user's locale.
 */

/**
 * @typedef {Object} EljeSharedSettings
 *
 * @property {string}                  adminUrl         The url for the current
 *                                                      site's dashboard.
 * @property {Object}                  countries        An object of countries
 *                                                      where the keys are
 *                                                      Country codes and values
 *                                                      are country names
 *                                                      localized for the site's
 *                                                      current language.
 * @property {string}                  defaultDateRange The default date range
 *                                                      query string to use.
 * @property {EljeSiteLocale}   locale           Locale information for
 *                                                      the site.
 * @property {Object}                  orderStatuses    An object of order
 *                                                      statuses indexed by
 *                                                      status key and localized
 *                                                      status value.
 * @property {string}                  siteTitle        The current title of the
 *                                                      site.
 * @property {string}                  eljeAssetUrl       The url to the assets
 *                                                      directory for the
 *                                                      Elje plugin.
 */

export {};
