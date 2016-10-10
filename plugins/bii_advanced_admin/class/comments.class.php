<?php

class comments extends global_class {

	protected $comment_ID;
	protected $comment_post_ID;
	protected $comment_author;
	protected $comment_author_email;
	protected $comment_author_url;
	protected $comment_author_IP;
	protected $comment_date;
	protected $comment_date_gmt;
	protected $comment_content;
	protected $comment_karma;
	protected $comment_approved;
	protected $comment_agent;
	protected $comment_type;
	protected $comment_parent;
	protected $user_id;

	public static function identifiant() {
		return "comment_ID";
	}

	public static function delete_not_approved(){
		static::deleteWhere("comment_approved = 0");
	}
}
