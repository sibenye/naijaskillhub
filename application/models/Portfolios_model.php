<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once (APPPATH . '/core/validations/Portfolios_validation.php');
require_once (APPPATH . '/core/exceptions/NSH_Exception.php');
require_once (APPPATH . '/core/exceptions/NSH_ResourceNotFoundException.php');
require_once (APPPATH . '/core/exceptions/NSH_ValidationException.php');
class Portfolios_model extends CI_Model {
    public function __construct() {
        $this->load->database();
    }
    public function get_portfolios($id = NULL, $categoryId = NULL) {
        $results = array ();
        $request = NULL;
        if ($id) {
            $request ['id'] = $id;
        }
        
        if ($categoryId) {
            $request ['categoryId'] = $categoryId;
        }
        
        if ($request) {
            $results = $this->db->get_where(PORTFOLIOS_TABLE, $request)->result_array();
        } else {
            $results = $this->db->get(PORTFOLIOS_TABLE)->result_array();
        }
        
        if (! $results || count($results) == 0 || $results [0] == NULL) {
            $message = 'No portfolios found';
            throw new NSH_ResourceNotFoundException(220, $message);
        }
        
        foreach ($results as $key => $value) {
            // retrieve images and videos
            $videos = $this->db->get_where(USERS_VIDEOS_PORTFOLIO_TABLE, array (
                    'portfolioId' => $results [$key] ['id']
            ))->result_array();
            $images = $this->db->get_where(USERS_IMAGES_PORTFOLIO_TABLE, array (
                    'portfolioId' => $results [$key] ['id']
            ))->result_array();
            
            $results [$key] ['videos'] = $videos;
            $results [$key] ['images'] = $images;
        }
        
        return $results;
    }
    public function get_portfolios_by_userId($userId) {
        $results = array();
        $videos = array();
        $images = array();
        $credits = array();
        $voiceClips = array();
        $categories = array();
        
        $this->db->select('id AS videoPortfolioId, videoUrl, caption');
        $videos = $this->db->get_where(USERS_VIDEOS_PORTFOLIO_TABLE, array(
                'userId' => $userId
        ))->result_array();
        
        $this->db->select('id AS imagePortfolioId, imageUrl, caption');
        $images = $this->db->get_where(USERS_IMAGES_PORTFOLIO_TABLE, array(
                'userId' => $userId
        ))->result_array();
        
        $this->db->select('id AS creditPortfolioId, year, caption, creditTypeId');
        $credits = $this->db->get_where(USERS_CREDITS_PORTFOLIO_TABLE, array(
                'userId' => $userId
        ))->result_array();
        
        $this->db->select('id AS voiceClipPortfolioId, clipUrl, caption');
        $voiceClips = $this->db->get_where(USERS_VOICECLIPS_PORTFOLIO_TABLE, array(
                'userId' => $userId
        ))->result_array();
        
        $this->db->select('categoryId, name as categoryName');
        $this->db->from(USERS_CATEGORIES_PORTFOLIO_TABLE);
        $this->db->join(CATEGORIES_TABLE, CATEGORIES_TABLE.'.id = '.USERS_CATEGORIES_PORTFOLIO_TABLE.'.categoryId');
        $this->db->where(array('userId' => $userId));
        $categories = $this->db->get()->result_array();
        
        $results ['videos'] = $videos;
        $results ['images'] = $images;
        $results ['voiceClips'] = $voiceClips;
        $results ['credits'] = $credits;
        $results ['categories'] = $categories;
        
        return $results;
    }
    public function upsert_portfolio($portfolio, $userId) {
        // validate the portfolio collections
        $this->validatePortfolioPostData($portfolio, $userId);
        
        if (array_key_exists('images', $portfolio)) {
            $this->save_portfolio_images($userId, $portfolio ['images']);
        }
        
        if (array_key_exists('videos', $portfolio)) {
            $this->save_portfolio_videos($userId, $portfolio ['videos']);
        }
        
        if (array_key_exists('categories', $portfolio)) {
            $this->save_portfolio_categories($userId, $portfolio ['categories']);
        }
        
        if (array_key_exists('voiceClips', $portfolio)) {
            $this->save_portfolio_voiceClips($userId, $portfolio ['voiceClips']);
        }
        
        if (array_key_exists('credits', $portfolio)) {
            $this->save_portfolio_credits($userId, $portfolio ['credits']);
        }
        
        return;
    }
    public function delete_portfolio($portfolios, $userId) {
        if (empty($portfolios)) {
            // delete all artifacts associated with user
            $this->delete_portfolio_images($userId, NULL);
            $this->delete_portfolio_videos($userId, NULL);
            $this->delete_portfolio_categories($userId, NULL);
            $this->delete_portfolio_voiceClips($userId, NULL);
            $this->delete_portfolio_credits($userId, NULL);
        } else {
            if (array_key_exists('images', $portfolios)) {
                $this->delete_portfolio_images($userId, $portfolios ['images']);
            }
            
            if (array_key_exists('videos', $portfolios)) {
                $this->delete_portfolio_videos($userId, $portfolios ['videos']);
            }
            
            if (array_key_exists('categories', $portfolios)) {
                $this->delete_portfolio_categories($userId, $portfolios ['categories']);
            }
            
            if (array_key_exists('voiceClips', $portfolios)) {
                $this->delete_portfolio_voiceClips($userId, $portfolios ['voiceClips']);
            }
            
            if (array_key_exists('credits', $portfolios)) {
                $this->delete_portfolio_credits($userId, $portfolios ['credits']);
            }
        }
    }
    public function validatePortfolioPostData($post_data, $userId) {
        if (array_key_exists('images', $post_data)) {
            // validate image collection
            if (! empty($post_data ['images'])) {
                foreach ($post_data ['images'] as $image) {
                    if (! array_key_exists('imageUrl', $image) || empty($image ['imageUrl'])) {
                        $error_message = 'image imageUrl is required';
                        throw new NSH_ValidationException(110, $error_message);
                    }
                    
                    if (array_key_exists('imagePortfolioId', $image)) {
                        // ensure the portfolioId exists
                        if (empty($this->db->get_where(USERS_IMAGES_PORTFOLIO_TABLE, array (
                                'id' => $image ['imagePortfolioId']
                        ))->result_array())) {
                            throw new NSH_ResourceNotFoundException(220, "imagePortfolioId '".$image ['imagePortfolioId']."' not found");
                        }
                    }
                }
            }
        }
        
        // validate video collection
        if (! empty($post_data ['videos'])) {
            foreach ($post_data ['videos'] as $video) {
                if (! array_key_exists('videoUrl', $video) || empty($video ['videoUrl'])) {
                    $error_message = 'video videoUrl field is required';
                    throw new NSH_ValidationException(110, $error_message);
                }
                
                if (array_key_exists('videoPortfolioId', $video)) {
                    // ensure the portfolioId exists
                    if (empty($this->db->get_where(USERS_VIDEOS_PORTFOLIO_TABLE, array (
                            'id' => $video ['videoPortfolioId']
                    ))->row_array())) {
                        throw new NSH_ResourceNotFoundException(220, "videoPortfolioId '".$video ['videoPortfolioId']."' not found");
                    }
                }
            }
        }
        
        // validate voiceClip collection
        if (! empty($post_data ['voiceClips'])) {
            foreach ($post_data ['voiceClips'] as $voiceClip) {
                if (! array_key_exists('clipUrl', $voiceClip) || empty($voiceClip ['clipUrl'])) {
                    $error_message = 'voiceClip clipUrl field is required';
                    throw new NSH_ValidationException(110, $error_message);
                }
                
                if (! array_key_exists('caption', $voiceClip) || empty($voiceClip ['caption'])) {
                    $error_message = 'voiceClip caption field is required';
                    throw new NSH_ValidationException(110, $error_message);
                }
                
                if (array_key_exists('voiceClipPortfolioId', $voiceClip)) {
                    // ensure the portfolioId exists
                    if (empty($this->db->get_where(USERS_VOICECLIPS_PORTFOLIO_TABLE, array (
                            'id' => $voiceClip ['voiceClipPortfolioId']
                    ))->row_array())) {
                        throw new NSH_ResourceNotFoundException(220, "voiceClipPortfolioId '".$voiceClip ['voiceClipPortfolioId']."' not found");
                    }
                }
            }
        }
        
        // validate credits collection
        if (! empty($post_data ['credits'])) {
            foreach ($post_data ['credits'] as $credit) {
                if (! array_key_exists('year', $credit) || empty($credit ['year'])) {
                    $error_message = 'credits year field is required';
                    throw new NSH_ValidationException(110, $error_message);
                }
                
                if (! array_key_exists('caption', $credit) || empty($credit ['caption'])) {
                    $error_message = 'credits caption field is required';
                    throw new NSH_ValidationException(110, $error_message);
                }
                
                if (! array_key_exists('creditTypeId', $credit) || empty($credit ['creditTypeId'])) {
                    $error_message = 'credits creditTypeId field is required';
                    throw new NSH_ValidationException(110, $error_message);
                } else {
                    //ensure the creditTypeId is valid
                    if (empty($this->db->get_where(CREDITTYPES_TABLE, array (
                            'id' => $credit ['creditTypeId']
                    ))->row_array())) {
                        throw new NSH_ResourceNotFoundException(220, "creditTypeId '".$credit['creditTypeId']."' not found");
                    }
                }
                
                if (array_key_exists('creditPortfolioId', $credit)) {
                    // ensure the portfolioId exists
                    if (empty($this->db->get_where(USERS_CREDITS_PORTFOLIO_TABLE, array (
                            'id' => $credit ['creditPortfolioId']
                    ))->row_array())) {
                        throw new NSH_ResourceNotFoundException(220, "creditPortfolioId '".$credit['creditPortfolioId']."' not found");
                    }
                }
            }
        }
        
        // validate categoryIds collection
        if (! empty($post_data ['categories'])) {
            foreach ($post_data ['categories'] as $categoryId) {
                if (empty($this->db->get_where(CATEGORIES_TABLE, array (
                        'id' => $categoryId
                ))->result_array())) {
                    throw new NSH_ResourceNotFoundException(220, "categoryId '" . $categoryId . "' not found");
                }
            }
        }
    }
    private function save_portfolio_images($userId, $image_collection) {
        foreach ($image_collection as $value) {
            $data = array (
                    'imageUrl' => $value ['imageUrl']
            );
            
            if (array_key_exists('caption', $value)) {
                $data ['caption'] = $value ['caption'];
            }
            if (array_key_exists('imagePortfolioId', $value)) {
                $this->db->update(USERS_IMAGES_PORTFOLIO_TABLE, $data, array (
                        'id' => $value ['imagePortfolioId']
                ));
            } else {
                $data ['userId'] = $userId;
                $this->db->insert(USERS_IMAGES_PORTFOLIO_TABLE, $data);
            }
        }
    }
    private function save_portfolio_videos($userId, $video_collection) {
        foreach ($video_collection as $value) {
            $data = array (
                    'videoUrl' => $value ['videoUrl']
            );
            if (array_key_exists('caption', $value)) {
                $data ['caption'] = $value ['caption'];
            }
            if (array_key_exists('videoPortfolioId', $value)) {
                $this->db->update(USERS_VIDEOS_PORTFOLIO_TABLE, $data, array (
                        'id' => $value ['videoPortfolioId']
                ));
            } else {
                $data ['userId'] = $userId;
                $this->db->insert(USERS_VIDEOS_PORTFOLIO_TABLE, $data);
            }
        }
    }
    private function save_portfolio_voiceClips($userId, $voiceClip_collection) {
        foreach ($voiceClip_collection as $value) {
            $data = array (
                    'clipUrl' => $value ['clipUrl'],
                    'caption' => $value ['caption']
            );
            if (array_key_exists('voiceClipPortfolioId', $value)) {
                $this->db->update(USERS_VOICECLIPS_PORTFOLIO_TABLE, $data, array (
                        'id' => $value ['voiceClipPortfolioId']
                ));
            } else {
                $data ['userId'] = $userId;
                $this->db->insert(USERS_VOICECLIPS_PORTFOLIO_TABLE, $data);
            }
        }
    }
    private function save_portfolio_credits($userId, $credit_collection) {
        foreach ($credit_collection as $value) {
            
            $data = array (
                    'creditTypeId' => $value ['creditTypeId'],
                    'year' => $value ['year'],
                    'caption' => $value ['caption']
            );
            
            // if this is an update ensure the portfolio Id exists
            if (array_key_exists('creditPortfolioId', $value)) {
                $this->db->update(USERS_CREDITS_PORTFOLIO_TABLE, $data, array (
                        'id' => $value ['creditPortfolioId']
                ));
            } else {
                $data ['userId'] = $userId;
                $this->db->insert(USERS_CREDITS_PORTFOLIO_TABLE, $data);
            }
        }
    }
    private function save_portfolio_categories($userId, $categoryIds_array) {
        $existingPortfolioCategories = $this->db->get_where(USERS_CATEGORIES_PORTFOLIO_TABLE, array (
                'userId' => $userId
        ))->result_array();
        $existingCategoryIds = array ();
        
        foreach ($existingPortfolioCategories as $key => $value) {
            $existingCategoryIds [$key] = $value ['categoryId'];
        }
        
        // insert new portfolio categoryIds only
        foreach ($categoryIds_array as $value) {
            if (! in_array($value, $existingCategoryIds)) {
                $data = array (
                        'userId' => $userId,
                        'categoryId' => $value
                );
                $this->db->insert(USERS_CATEGORIES_PORTFOLIO_TABLE, $data);
            }
        }
    }
    private function delete_portfolio_images($userId, $imagePortfolioIds = NULL) {
        if ($imagePortfolioIds == NULL) {
            $this->db->delete(USERS_IMAGES_PORTFOLIO_TABLE, array (
                    'userId' => $userId
            ));
        } else {
            
            foreach ($imagePortfolioIds as $value) {
                $this->db->delete(USERS_IMAGES_PORTFOLIO_TABLE, array (
                        'userid' => $userId,
                        'id' => $value
                ));
            }
        }
    }
    private function delete_portfolio_videos($userId, $videoPortfolioIds = NULL) {
        if ($videoPortfolioIds == NULL) {
            $this->db->delete(USERS_VIDEOS_PORTFOLIO_TABLE, array (
                    'userId' => $userId
            ));
        } else {
            foreach ($videoPortfolioIds as $value) {
                $this->db->delete(USERS_VIDEOS_PORTFOLIO_TABLE, array (
                        'userId' => $userId,
                        'id' => $value
                ));
            }
        }
    }
    private function delete_portfolio_voiceClips($userId, $clipPortfolioIds_array = NULL) {
        if ($clipPortfolioIds_array == NULL) {
            $this->db->delete(USERS_VOICECLIPS_PORTFOLIO_TABLE, array (
                    'userId' => $userId
            ));
        } else {
            foreach ($clipPortfolioIds_array as $value) {
                $this->db->delete(USERS_VOICECLIPS_PORTFOLIO_TABLE, array (
                        'userId' => $userId,
                        'id' => $value
                ));
            }
        }
    }
    private function delete_portfolio_credits($userId, $creditsPortfolioIds = NULL) {
        if ($creditsPortfolioIds == NULL) {
            $this->db->delete(USERS_CREDITS_PORTFOLIO_TABLE, array (
                    'userId' => $userId
            ));
        } else {
            
            foreach ($creditsPortfolioIds as $value) {
                $this->db->delete(USERS_CREDITS_PORTFOLIO_TABLE, array (
                        'userid' => $userId,
                        'id' => $value
                ));
            }
        }
    }
    private function delete_portfolio_categories($userId, $categoryIds_array = NULL) {
        if ($categoryIds_array == NULL) {
            $this->db->delete(USERS_CATEGORIES_PORTFOLIO_TABLE, array (
                    'userId' => $userId
            ));
        } else {
            foreach ($categoryIds_array as $value) {
                $this->db->delete(USERS_CATEGORIES_PORTFOLIO_TABLE, array (
                        'userId' => $userId,
                        'categoryId' => $value
                ));
            }
        }
    }
}