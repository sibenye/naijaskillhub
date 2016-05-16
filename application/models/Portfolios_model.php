<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/validations/Portfolios_validation.php');
require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class Portfolios_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_portfolios($id = NULL, $categoryId = NULL)
		{
			$results = array();
	        if (!$id && !$categoryId)
	        {
                $query = $this->db->get('portfolios');
				$results = $query->result_array();
	        }elseif ($id && $categoryId){
	        	$query = $this->db->get_where('portfolios', array('id' => $id, 'categoryId' => $categoryId));
				$results[0] = $query->row_array();
	        }elseif ($categoryId){
	        	$query = $this->db->get_where('portfolios', array('categoryId' => $categoryId));
				$results = $query->result_array();
	        }else{
	        	$query = $this->db->get_where('portfolios', array('id' => $id));					
				$results[0] = $query->row_array();
	        }
			
			if (!$results || count($results) == 0 || $results[0] == NULL){
				$message = 'No portfolios found';
				throw new NSH_ResourceNotFoundException($message);
			}
			
			foreach ($results as $key => $value) {
				//retrieve images and videos
				$videos = $this->db->get_where('portfolios_videos_link', array('portfolioId' => $results[$key]['id']))->result_array();
				$images = $this->db->get_where('portfolios_images_link', array('portfolioId' => $results[$key]['id']))->result_array();
				
				$results[$key]['videos'] = $videos;
				$results[$key]['images'] = $images;
			}
	
			return $results;        
		}
		
		public function save_portfolio($post_data)
		{
			//validate post data
			$this->validation = new Portfolios_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				throw new NSH_ValidationException($this->form_validation->error_array());
			}

			//ensure that the category Id exists
			$existingCategory = $this->db->get_where('categories', array('id' => $post_data['categoryId']))->row_array();
			if(!$existingCategory || empty($existingCategory)){
				$error_message = "Category does not exist";
				show_validation_exception($error_message);
				throw new NSH_ResourceNotFoundException($error_message);
			}
			
			//ensure that the user exists
			$existingUser = $this->db->get_where('users', array('id' => $post_data['userId']))->row_array();
			if(!$existingUser || empty($existingUser)){
				$error_message = "User does not exist";
				show_validation_exception($error_message);
				throw new NSH_ResourceNotFoundException($error_message);
			}
			
			//check if user already has a portfolio in this category
			$existingPortfolio = $this->db->get_where('portfolios', array('userId' => $existingUser['id'], 'categoryId' => $existingCategory['id']))->row_array();
			
			$this->load->helper('date');
			
			$datestring = '%Y/%m/%d %H:%i:%s';
			$time = time();
		
			$nowDate = mdate($datestring, $time);
			
			if ($existingPortfolio && !empty($existingPortfolio))
	        {
	        	$id = $existingPortfolio['id'];
	        	$data = array(
	        	'modifiedDate' => $nowDate);
				
				$this->db->update('portfolios', $data, array('id' => $id));
			} else{
				$data = array(
			        'categoryId' => $post_data['categoryId'],
			        'userId' => $post_data['userId'],
			        'createdDate' => $nowDate,
			        'modifiedDate' => $nowDate
			    );
		
		    	$this->db->insert('portfolios', $data);
		    	$existingPortfolio = $this->db->get_where('portfolios', array('userId' => $existingUser['id'], 'categoryId' => $existingCategory['id']))->row_array();
				$id = $existingPortfolio['id'];
			}			
				
			if (array_key_exists('images', $post_data) && $post_data['images']){
				$this->save_portfolio_images($id, $post_data);
			} else {
				$this->delete_portfolio_images($id);
			}
			
			if (array_key_exists('videos', $post_data) && $post_data['videos']){
				$this->save_portfolio_videos($id, $post_data);
			} else {
				$this->delete_portfolio_videos($id);
			}
			
			return;
		}
		
		public function delete_portfolio($id)
		{
			//first delete any associated images or videos
			$this->delete_portfolio_videos($id);
			$this->delete_portfolio_images($id);
			
			$result = $this->db->delete('portfolios', array('id' => $id));
			
			if($result === FALSE)
	        {
				throw new NSH_Exception('failed to delete portfolio');
	        }
		}
		
		private function save_portfolio_images($portfolioId, $post_data){
			$portfolioImagesInRequest = array();
			for ($i=0, $size = count($post_data['images']); $i < $size; $i++) { 
				$portfolioImagesInRequest[$i] = strtolower($post_data['images'][$i]['imageUrl']);
			}

			$existingPortfolioImages = $this->db->get_where('portfolios_images_link', array('portfolioId' => $portfolioId))->result_array();
			$existImageUrls = array();
			
			//delete existing portfolio images that are not in the request
			foreach ($existingPortfolioImages as $key => $value) {
				$existImageUrls[$key] = $value['imageUrl'];
				if (!in_array(strtolower($value['imageUrl']), $portfolioImagesInRequest)){
					$this->delete_portfolio_images($portfolioId, $value['imageUrl']);
				}
			}
			
			//insert new portfolio images only
			foreach ($portfolioImagesInRequest as $value) {
				if (!in_array($value, $existImageUrls)){
					$data = array('portfolioId' => $portfolioId, 'imageUrl' => $value);
					$this->db->insert('portfolios_images_link', $data);
				}
			}			
		}
		
		private function save_portfolio_videos($portfolioId, $post_data){
			$portfolioVideosInRequest = array();
			for ($i=0, $size = count($post_data['videos']); $i < $size; $i++) { 
				$portfolioVideosInRequest[$i] = strtolower($post_data['videos'][$i]['videoUrl']);
			}

			$existingPortfolioVideos = $this->db->get_where('portfolios_videos_link', array('portfolioId' => $portfolioId))->result_array();
			$existVideoUrls = array();
			
			//delete existing portfolio videos that are not in the request
			foreach ($existingPortfolioVideos as $key => $value) {
				$existVideoUrls[$key] = $value['videoUrl'];
				if (!in_array(strtolower($value['videoUrl']), $portfolioVideosInRequest)){
					$this->delete_portfolio_videos($portfolioId, $value['videoUrl']);
				}
			}
			
			//insert new portfolio videos only
			foreach ($portfolioVideosInRequest as $value) {
				if (!in_array($value, $existVideoUrls)){
					$data = array('portfolioId' => $portfolioId, 'videoUrl' => $value);
					$this->db->insert('portfolios_videos_link', $data);
				}
			}			
		}
		
		private function delete_portfolio_images($portfolioId, $imageUrl = NULL)
		{
			if ($imageUrl == NULL){
				$result = $this->db->delete('portfolios_images_link', array('portfolioId' => $portfolioId));
			} else {
				$result = $this->db->delete('portfolios_images_link', array('portfolioId' => $portfolioId, 'imageUrl' => $imageUrl));
			}			
		}
		
		private function delete_portfolio_videos($portfolioId, $videoUrl = NULL)
		{
			if ($videoUrl == NULL){
				$result = $this->db->delete('portfolios_videos_link', array('portfolioId' => $portfolioId));
			} else {
				$result = $this->db->delete('portfolios_videos_link', array('portfolioId' => $portfolioId, 'videoUrl' => $videoUrl));
			}			
		}
		
		
}