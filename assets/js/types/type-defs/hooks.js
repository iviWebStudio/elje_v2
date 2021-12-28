/**
 * @typedef {import('./contexts').StoreNoticeObject} StoreNoticeObject
 */

/**
 * @typedef {Object} CheckoutNotices
 *
 * @property {StoreNoticeObject[]} checkoutNotices       Array of notices in the
 *                                                       checkout context.
 */

/**
 * @typedef {Object} EmitResponseTypes
 *
 * @property {string} SUCCESS To indicate a success response.
 * @property {string} FAIL    To indicate a failed response.
 * @property {string} ERROR   To indicate an error response.
 */

/**
 * @typedef {Object} EmitSuccessResponse
 *
 * @property {EmitResponseTypes['SUCCESS']} type          Should have the value of
 *                                                        EmitResponseTypes.SUCCESS.
 * @property {string}                       [redirectUrl] If the redirect url should be changed set
 *                                                        this. Note, this is ignored for some
 *                                                        emitters.
 * @property {Object}                       [meta]        Additional data returned for the success
 *                                                        response. This varies between context
 *                                                        emitters.
 */

/**
 * @typedef {Object} EmitFailResponse
 *
 * @property {EmitResponseTypes['FAIL']} type             Should have the value of
 *                                                        EmitResponseTypes.FAIL
 * @property {string}                    message          A message to trigger a notice for.
 * @property {NoticeContextsEnum}        [messageContext] What context to display any message in.
 * @property {Object}                    [meta]           Additional data returned for the fail
 *                                                        response. This varies between context
 *                                                        emitters.
 */

/**
 * @typedef {Object} EmitErrorResponse
 *
 * @property {EmitResponseTypes['ERROR']} type               Should have the value of
 *                                                           EmitResponseTypes.ERROR
 * @property {string}                     message            A message to trigger a notice for.
 * @property {boolean}                    retry              If false, then it means an
 *                                                           irrecoverable error so don't allow for
 *                                                           shopper to retry checkout (which may
 *                                                           mean either a different payment or
 *                                                           fixing validation errors).
 * @property {Object}                     [validationErrors] If provided, will be set as validation
 *                                                           errors in the validation context.
 * @property {NoticeContextsEnum}         [messageContext]   What context to display any message in.
 * @property {Object}                     [meta]             Additional data returned for the fail
 *                                                           response. This varies between context
 *                                                           emitters.
 */
/* eslint-enable jsdoc/valid-types */

/**
 * @typedef {Object} EmitResponseApi
 *
 * @property {EmitResponseTypes}        responseTypes     An object of various response types that can
 *                                                        be used in returned response objects.
 * @property {NoticeContexts}           noticeContexts    An object of various notice contexts that can
 *                                                        be used for targeting where a notice appears.
 * @property {function(Object):boolean} shouldRetry       Returns whether the user is allowed to retry
 *                                                        the payment after a failed one.
 * @property {function(Object):boolean} isSuccessResponse Returns whether the given response is of a
 *                                                        success response type.
 * @property {function(Object):boolean} isErrorResponse   Returns whether the given response is of an
 *                                                        error response type.
 * @property {function(Object):boolean} isFailResponse    Returns whether the given response is of a
 *                                                        fail response type.
 */

export {};
