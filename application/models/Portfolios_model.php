<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/core/validations/Portfolios_validation.php');
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
			$request = NULL;
			if ($id)
			{
				$request['id'] = $id;
			}
			
			if ($categoryId)
			{
				$request['categoryId'] = $categoryId;
			}
				
			if ($request)
			{
				$results = $this->db->get_where(PORTFOLIOS_TABLE, $request)->result_array();
			}
			else 
			{
				$results = $this->db->get(PORTFOLIOS_TABLE)->result_array();
			}
	        			
			if (!$results || count($results) == 0 || $results[0] == NULL){
				$message = 'No portfolios found';
				throw new NSH_ResourceNotFoundException(220, $message);
			}
			
			foreach ($results as $key => $value) {
				//retrieve images and videos
				$videos = $this->db->get_where(PORTFOLIOS_VIDEOS_LINK_TABLE, array('portfolioId' => $results[$key]['id']))->result_array();
				$images = $this->db->get_where(PORTFOLIOS_IMAGES_LINK_TABLE, array('portfolioId' => $results[$key]['id']))->result_array();
				
				$results[$key]['videos'] = $videos;
				$results[$key]['images'] = $images;
			}
	
			return $results;        
		}
		
		public function get_portfolios_by_userId($get_data)
		{
			$results = array();
			$request = array('userId' => $get_data['userId']);
			if (array_key_exists('id', $get_data))
			{
				$request['id'] = $get_data['id'];
			}
			
			if (array_key_exists('categoryId', $get_data))
			{
				$request['categoryId'] = $get_data['categoryId'];
			}
			
			$query = $this->db->get_where(PORTFOLIOS_TABLE, $request);
			$results = $query->result_array();
			
			if (!$results || count($results) == 0 || $results[0] == NULL){
				$message = 'No portfolios found';
				throw new NSH_ResourceNotFoundException(220, $message);
			}
				
			foreach ($results as $key => $value) {
				//retrieve images and videos
				$videos = $this->db->get_where(PORTFOLIOS_VIDEOS_LINK_TABLE, array('portfolioId' => $results[$key]['id']))->result_array();
				$images = $this->db->get_where(PORTFOLIOS_IMAGES_LINK_TABLE, array('portfolioId' => $results[$key]['id']))->result_array();
		
				$results[$key]['videos'] = $videos;
				$results[$key]['images'] = $images;
			}
		
			return $results;
		}
		
		public function upsert_portfolio($portfolio, $userId)
		{
			$id = NULL;
			if (array_key_exists('id', $portfolio) && !empty($portfolio['id']))
			{
				$id = $portfolio['id'];
			}
			
			$this->load->helper('date');
			$nowDate = mdate(DATE_TIME_STRING, time());
			
			if (!empty($id))
	        {
	        	$data = array(
	        			'categoryId' => $portfolio['categoryId'],
	        			'modifiedDate' => $nowDate	        			
	        	);
				
				$this->db->update(PORTFOLIOS_TABLE, $data, array('id' => $id));
			} else{
				$data = array(
			        'categoryId' => $portfolio['categoryId'],
			        'userId' => $userId,
			        'createdDate' => $nowDate,
			        'modifiedDate' => $nowDate
			    );
		
		    	$this->db->insert(PORTFOLIOS_TABLE, $data);
		    	$existingPortfolio = $this->db->get_where(PORTFOLIOS_TABLE, array('userId' => $userId, 'categoryId' => $portfolio['categoryId']))->row_array();
				$id = $existingPortfolio['id'];
			}			
				
			if (array_key_exists('images', $portfolio)){
				$this->save_portfolio_images($id, $portfolio);
			}
			
			if (array_key_exists('videos', $portfolio)){
				$this->save_portfolio_videos($id, $portfolio);
			}
			
			return;
		}
		
		public function delete_portfolio($id)
		{
			//first delete any associated images or videos
			$this->delete_portfolio_videos($id);
			$this->delete_portfolio_images($id);
			
			$result = $this->db->delete(PORTFOLIOS_TABLE, array('id' => $id));
			
			if($result === FALSE)
	        {
				throw new NSH_Exception(100, 'failed to delete portfolio');
	        }
		}
		
		public function validatePortfolioPostData($post_data, $userId, $isUpdate = FALSE)
		{
			if (!array_key_exists('categoryId', $post_data) || empty($post_data['categoryId']))
			{
				$error_message = 'The Category Id is required';
				throw new NSH_ValidationException(110, $error_message);
			}
			
			if ($isUpdate)
			{
				if (!array_key_exists('id', $post_data) || empty($post_data['id']))
				{
					$error_message = 'The Portfolio Id is required';
					throw new NSH_ValidationException(110, $error_message);
				}
				
				//check if portfolio exists
				$existingPortfolio = $this->db->get_where(PORTFOLIOS_TABLE, array('id' => $post_data['id']))->row_array();
				
				if (empty($existingPortfolio))
				{
					$error_message = 'Portfolio does not exist';
					throw new NSH_ValidationException(220);
				}
			}
				
			//ensure that the category Id exists
			$existingCategory = $this->db->get_where(CATEGORIES_TABLE, array('id' => $post_data['categoryId']))->row_array();
			if(!$existingCategory || empty($existingCategory)){
				$error_message = "Category does not exist";
				throw new NSH_ResourceNotFoundException(220, $error_message);
			}
				
			//check if user already has a portfolio in this category
			$existingPortfolio = $this->db->get_where(PORTFOLIOS_TABLE, array('userId' => $userId, 'categoryId' => $post_data['categoryId']))->row_array();
				
			if (!empty($existingPortfolio))
			{
				if ($isUpdate && $post_data['id'] != $existingPortfolio['id'])
				{
					throw new NSH_ValidationException(230);
				}
				elseif (!$isUpdate)
				{
					throw new NSH_ValidationException(230);
				}
			}
		}		
		
		private function save_portfolio_images($portfolioId, $post_data){
			//if images collection is empty, then delete all images associated with this portfolio
			if (empty($post_data['images']))
			{
				$this->delete_portfolio_images($id);
				return;
			}
			
			$portfolioImagesInRequest = array();
			for ($i=0, $size = count($post_data['images']); $i < $size; $i++) { 
				$portfolioImagesInRequest[$i] = strtolower($post_data['images'][$i]['imageUrl']);
			}

			$existingPortfolioImages = $this->db->get_where(PORTFOLIOS_IMAGES_LINK_TABLE, array('portfolioId' => $portfolioId))->result_array();
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
					$this->db->insert(PORTFOLIOS_IMAGES_LINK_TABLE, $data);
				}
			}			
		}
		
		private function save_portfolio_videos($portfolioId, $post_data){
			//if videos collection is empty, then delete all videos associated with this portfolio
			if (empty($post_data['videos']))
			{
				$this->delete_portfolio_videos($id);
				return;
			}
			$portfolioVideosInRequest = array();
			for ($i=0, $size = count($post_data['videos']); $i < $size; $i++) { 
				$portfolioVideosInRequest[$i] = strtolower($post_data['videos'][$i]['videoUrl']);
			}

			$existingPortfolioVideos = $this->db->get_where(PORTFOLIOS_VIDEOS_LINK_TABLE, array('portfolioId' => $portfolioId))->result_array();
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
					$this->db->insert(PORTFOLIOS_VIDEOS_LINK_TABLE, $data);
				}
			}			
		}
		
		private function delete_portfolio_images($portfolioId, $imageUrl = NULL)
		{
			if ($imageUrl == NULL){
				$result = $this->db->delete(PORTFOLIOS_IMAGES_LINK_TABLE, array('portfolioId' => $portfolioId));
			} else {
				$result = $this->db->delete(PORTFOLIOS_IMAGES_LINK_TABLE, array('portfolioId' => $portfolioId, 'imageUrl' => $imageUrl));
			}			
		}
		
		private function delete_portfolio_videos($portfolioId, $videoUrl = NULL)
		{
			if ($videoUrl == NULL){
				$result = $this->db->delete(PORTFOLIOS_VIDEOS_LINK_TABLE, array('portfolioId' => $portfolioId));
			} else {
				$result = $this->db->delete(PORTFOLIOS_VIDEOS_LINK_TABLE, array('portfolioId' => $portfolioId, 'videoUrl' => $videoUrl));
			}			
		}		
		
}