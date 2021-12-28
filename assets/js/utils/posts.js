/**
 * Get the src of the the featured image of the post.
 *
 * @param {Object} post The post object to get the thumbnail from.
 * @param {Object} post.thumbnail The post thumbnail object, destructured from the post object.
 * @param {string} post.thumbnail.src The thumbnail src.
 * @return {string} The full URL to the image.
 */
export function getImageSrcFromPost(post) {
	if (!post || !post.thumbnail) {
		return '';
	}

	return post.thumbnail.src || '';
}

/**
 * Get the ID of the the featured image of the post.
 *
 * @param {Object} post The post object to get the thumbnail from.
 * @param {Object} post.thumbnail The post thumbnail object, destructured from the post object.
 * @param {number} post.thumbnail.id The thumbnail id.
 * @return {number} The ID of the image.
 */
export function getImageIdFromPost(post) {
	if (!post || !post.thumbnail) {
		return 0;
	}

	return post.thumbnail.id || 0;
}
