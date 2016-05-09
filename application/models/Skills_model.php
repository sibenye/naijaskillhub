<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/validations/Skills_validation.php');
require_once(APPPATH.'/core/exceptions/NSH_Exception.php');
require_once(APPPATH.'/core/exceptions/NSH_ResourceNotFoundException.php');
require_once(APPPATH.'/core/exceptions/NSH_ValidationException.php');

class Skills_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_skills($id = NULL, $categoryId = NULL)
		{
			$results = array();
	        if (!$id && !$categoryId)
	        {
                $query = $this->db->get('skills');
				$results = $query->result_array();
	        }elseif ($id && $categoryId){
	        	$query = $this->db->get_where('skills', array('id' => $id, 'categoryId' => $categoryId));
				$results[0] = $query->row_array();
	        }elseif ($categoryId){
	        	$query = $this->db->get_where('skills', array('categoryId' => $categoryId));
				$results = $query->result_array();
	        }else{
	        	$query = $this->db->get_where('skills', array('id' => $id));					
				$results[0] = $query->row_array();
	        }
			
			if (!$results || count($results) == 0){
				$message = 'No skills found';
				throw new NSH_ResourceNotFoundException($message);
			}
			
			foreach ($results as $key => $value) {
				//retrieve images and videos
				$videos = $this->db->get_where('skills_videos_link', array('skillId' => $results[$key]['id']))->result_array();
				$images = $this->db->get_where('skills_images_link', array('skillId' => $results[$key]['id']))->result_array();
				
				$results[$key]['videos'] = $videos;
				$results[$key]['images'] = $images;
			}						
	
			return $results;        
		}
		
		public function save_skill($post_data)
		{
			//validate post data
			$this->validation = new Skills_validation();
			$rules = $this->validation->validation_rules;
			
			$this->load->library('form_validation', $rules);
			$this->form_validation->validate($post_data);
			if ($this->form_validation->error_array()){
				throw new NSH_ValidationException($this->form_validation->error_array());
			}

			//ensure that the category Id exists
			$existingCategory = $this->db->get_where('skillCategories', array('id' => $post_data['categoryId']))->row_array();
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
			
			//check if user already has a skill in this category
			$existingSkill = $this->db->get_where('skills', array('userId' => $existingUser['id'], 'categoryId' => $existingCategory['id']))->row_array();
			
			$this->load->helper('date');
			
			$datestring = '%Y/%m/%d %H:%i:%s';
			$time = time();
		
			$nowDate = mdate($datestring, $time);
			
			if ($existingSkill && !empty($existingSkill))
	        {
	        	$id = $existingSkill['id'];
	        	$data = array(
	        	'modifiedDate' => $nowDate);
				
				$this->db->update('skills', $data, array('id' => $id));
			} else{
				$data = array(
			        'categoryId' => $post_data['categoryId'],
			        'userId' => $post_data['userId'],
			        'createdDate' => $nowDate,
			        'modifiedDate' => $nowDate
			    );
		
		    	$this->db->insert('skills', $data);
		    	$existingSkill = $this->db->get_where('skills', array('userId' => $existingUser['id'], 'categoryId' => $existingCategory['id']))->row_array();
				$id = $existingSkill['id'];
			}			
				
			if (array_key_exists('images', $post_data) && $post_data['images']){
				$this->save_skill_images($id, $post_data);
			} else {
				$this->delete_skill_images($id);
			}
			
			if (array_key_exists('videos', $post_data) && $post_data['videos']){
				$this->save_skill_videos($id, $post_data);
			} else {
				$this->delete_skill_videos($id);
			}
			
			return;
		}
		
		public function delete_skill($id)
		{
			//first delete any associated images or videos
			$this->delete_skill_videos($id);
			$this->delete_skill_images($id);
			
			$result = $this->db->delete('skills', array('id' => $id));
			
			if($result === FALSE)
	        {
				throw new NSH_Exception('failed to delete skill');
	        }
		}
		
		private function save_skill_images($skillId, $post_data){
			$skillImagesInRequest = array();
			for ($i=0, $size = count($post_data['images']); $i < $size; $i++) { 
				$skillImagesInRequest[$i] = strtolower($post_data['images'][$i]['imageUrl']);
			}

			$existingSkillImages = $this->db->get_where('skills_images_link', array('skillId' => $skillId))->result_array();
			$existImageUrls = array();
			
			//delete existing skill images that are not in the request
			foreach ($existingSkillImages as $key => $value) {
				$existImageUrls[$key] = $value['imageUrl'];
				if (!in_array(strtolower($value['imageUrl']), $skillImagesInRequest)){
					$this->delete_skill_images($skillId, $value['imageUrl']);
				}
			}
			
			//insert new skill images only
			foreach ($skillImagesInRequest as $value) {
				if (!in_array($value, $existImageUrls)){
					$data = array('skillId' => $skillId, 'imageUrl' => $value);
					$this->db->insert('skills_images_link', $data);
				}
			}			
		}
		
		private function save_skill_videos($skillId, $post_data){
			$skillVideosInRequest = array();
			for ($i=0, $size = count($post_data['videos']); $i < $size; $i++) { 
				$skillVideosInRequest[$i] = strtolower($post_data['videos'][$i]['videoUrl']);
			}

			$existingSkillVideos = $this->db->get_where('skills_videos_link', array('skillId' => $skillId))->result_array();
			$existVideoUrls = array();
			
			//delete existing skill images that are not in the request
			foreach ($existingSkillVideos as $key => $value) {
				$existVideoUrls[$key] = $value['videoUrl'];
				if (!in_array(strtolower($value['videoUrl']), $skillVideosInRequest)){
					$this->delete_skill_videos($skillId, $value['videoUrl']);
				}
			}
			
			//insert new skill images only
			foreach ($skillVideosInRequest as $value) {
				if (!in_array($value, $existVideoUrls)){
					$data = array('skillId' => $skillId, 'videoUrl' => $value);
					$this->db->insert('skills_videos_link', $data);
				}
			}			
		}
		
		private function delete_skill_images($skillId, $imageUrl = NULL)
		{
			if ($imageUrl == NULL){
				$result = $this->db->delete('skills_images_link', array('skillId' => $skillId));
			} else {
				$result = $this->db->delete('skills_images_link', array('skillId' => $skillId, 'imageUrl' => $imageUrl));
			}			
		}
		
		private function delete_skill_videos($skillId, $videoUrl = NULL)
		{
			if ($videoUrl == NULL){
				$result = $this->db->delete('skills_videos_link', array('skillId' => $skillId));
			} else {
				$result = $this->db->delete('skills_videos_link', array('skillId' => $skillId, 'videoUrl' => $videoUrl));
			}			
		}
		
		
}