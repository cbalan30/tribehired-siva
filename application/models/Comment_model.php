<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define("LOCALDATAFILEPATH", "public/data/comments.json");
define("REMOTEDATAFILEPATH", "https://jsonplaceholder.typicode.com/comments");
define("POSTSENDPOINT", "https://jsonplaceholder.typicode.com/posts");

class Comment_model extends CI_Model
{
  private function getAllCommentData()
  {
    $jsonData = '[]';
    $file_headers = @get_headers(REMOTEDATAFILEPATH);
    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
    {
      $this->load->helper('url');
      $jsonData = file_get_contents(base_url().LOCALDATAFILEPATH);
    } else {
      $jsonData = file_get_contents(REMOTEDATAFILEPATH);
    }
    return json_decode($jsonData, TRUE);
  }

  private function filterArray($array, $index, $value)
  {
    if(is_array($array) && count($array)>0)
    {
      foreach(array_keys($array) as $key)
      {
        $temp[$key] = $array[$key][$index];
        if ($temp[$key] == $value)
        {
          $filteredArray[$key] = $array[$key];
        }
      }
    }
    return $filteredArray;
  }

  private function getPostsData($post_id = 0)
  {
    $jsonData = '[]';
    $file_headers = @get_headers(POSTSENDPOINT);
    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found')
    {
      $jsonData = '[]';
    } else {
      $endpoint = POSTSENDPOINT;
      if((int)$post_id > 0)
      {
        $endpoint = POSTSENDPOINT."/".$post_id;
      }
      $jsonData = file_get_contents($endpoint);
    }
    return json_decode($jsonData, TRUE);
  }

  private $_allComments = [];
  private function getPostsCommentCount($post_id = 0)
  {
    $commentsCount = 0;
    $postsComments = [];
    $allComments = [];
    if(count($this->_allComments) == 0)
    {
      $this->_allComments = $this->getAllCommentData();
    }
    $allComments = $this->_allComments;

    if((int)$post_id > 0 && is_array($allComments) && count($allComments) > 0)
    {
      $postsComments = $this->filterArray($allComments, 'postId', $post_id);
      $commentsCount = count($postsComments);
    }
    return $commentsCount;
  }



  public function getAllComments()
	{
    return $this->getAllCommentData();
	}

  public function getPosts($post_id = 0)
  {
    $rawData = $this->getPostsData($post_id);

    $formatedPosts = [];
    if((int)$post_id > 0)
    {
      if(is_array($rawData))
      {
        $post = [];
        $post['post_id'] = $rawData['id'];
        $post['post_title'] = $rawData['title'];
        $post['post_body'] = $rawData['body'];
        $post['total_number_of_comments'] = $this->getPostsCommentCount($post_id);
        array_push($formatedPosts, $post);
      }
    } else {
      if(is_array($rawData))
      {
        foreach ($rawData as $data)
        {
          $post = [];
          $post['post_id'] = $data['id'];
          $post['post_title'] = $data['title'];
          $post['post_body'] = $data['body'];
          $post['total_number_of_comments'] = $this->getPostsCommentCount($post['post_id']);
          array_push($formatedPosts, $post);
        }
      }
    }
    return $formatedPosts;
  }

  public function getPostComments($filters = array())
	{
    $filteredArray = $this->getAllCommentData();

    foreach ($filters as $key => $value)
    {
      $filteredArray = $this->filterArray($filteredArray, $key, $value);
    }
    return $filteredArray;
	}



}



?>
