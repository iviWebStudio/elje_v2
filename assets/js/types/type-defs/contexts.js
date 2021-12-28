/**
 * @typedef {Object} EditorDataContext
 *
 * @property {boolean} isEditor      Indicates whether in the editor context.
 * @property {number}  currentPostId The post ID being edited.
 * @property {Object}  previewData   Object containing preview data for the editor.
 * @property {function(string):Object} getPreviewData Get data by name.
 */

/**
 * @typedef {Object} ValidationContextError
 *
 * @property {number} id Error ID.
 * @property {string} message Error message.
 * @property {boolean} hidden Error visibility.
 *
 */

/**
 * @typedef {Object} ValidationContext
 *
 * @property {(id:string)=>ValidationContextError} getValidationError     Return validation error for the given property.
 * @property {function(Object)}         setValidationErrors      Receive an object of properties and  error messages as
 *                                                               strings and adds to the validation error state.
 * @property {function(string)}         clearValidationError     Clears a validation error for the given property name.
 * @property {function()}               clearAllValidationErrors Clears all validation errors currently in state.
 * @property {function(string)}         getValidationErrorId     Returns the css id for the
 *                                                               validation error using the given inputId string.
 * @property {function(string)}         hideValidationError      Sets the hidden prop of a specific error to true.
 * @property {function(string)}         showValidationError      Sets the hidden prop of a specific error to false.
 * @property {function()}               showAllValidationErrors  Sets the hidden prop of all errors to false.
 * @property {boolean}                  hasValidationErrors      True if there is at least one error.
 */

/**
 * @typedef StoreNoticeObject
 *
 * @property {string} type   The type of notice.
 * @property {string} status The status of the notice.
 * @property {string} id     The id of the notice.
 */

/**
 * @typedef NoticeContext
 *
 * @property {Array<StoreNoticeObject>}              notices              An array of notice objects.
 * @property {function(string,string,any):undefined} createNotice         Creates a notice for the given arguments.
 * @property {function(string, any):undefined}       createSnackbarNotice Creates a snackbar notice type.
 * @property {function(string,string=):undefined}    removeNotice         Removes a notice with the given id and context
 * @property {string}                                context              The current context identifier for the notice
 *                                                                        provider
 * @property {function(boolean):void}                setIsSuppressed      Consumers can use this setter to suppress
 */

/**
 * @typedef {Object} ContainerWidthContext
 *
 * @property {boolean} hasContainerWidth  True once the class name has been derived.
 * @property {string}  containerClassName The class name derived from the width of the container.
 * @property {boolean} isMobile           True if the derived container width is mobile.
 * @property {boolean} isSmall            True if the derived container width is small.
 * @property {boolean} isMedium           True if the derived container width is medium.
 * @property {boolean} isLarge            True if the derived container width is large.
 */

export {};
