export interface PostResponseItemBaseData {
	value: string;
	display?: string;
	hidden?: boolean;
}

export type PostResponseItemData =
	PostResponseItemBaseData
	&
	({ key: string; name?: never } | { key?: never; name: string });

export interface PostResponseImageItem {
	id: number;
	src: string;
	thumbnail: string;
	srcset: string;
	sizes: string;
	name: string;
	alt: string;
}

export interface PostResponseTermItem {
	id: number;
	name: string;
	slug: string;
	link?: string;
}

export interface PostResponseItem {
	id: number;
	name: string;
	parent: number;
	type: string;
	permalink: string;
	excerpt: string;
	content: string;
	thumbnail: PostResponseImageItem;
	categories: Array<PostResponseTermItem>;
	tags: Array<PostResponseTermItem>;
}
