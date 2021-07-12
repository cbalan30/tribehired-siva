<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Comment_model', 'commentModel');
	}

	// Get All Comments
	public function getAllComments()
 	{
		$allComments = $this->commentModel->getAllComments();
		header('Content-Type: application/json');
		echo json_encode($allComments, JSON_PRETTY_PRINT);
 	}

	// Get Posts
	public function posts($post_id = 0)
	{
		$results = $this->commentModel->getPosts($post_id);
		header('Content-Type: application/json');
		echo json_encode($results, JSON_PRETTY_PRINT);
	}

	// Filter comments by field
	public function postcomments()
	{
		$postData = $this->input->post();
		$results = $this->commentModel->getPostComments($postData);
		header('Content-Type: application/json');
		echo json_encode($results, JSON_PRETTY_PRINT);
	}
}
