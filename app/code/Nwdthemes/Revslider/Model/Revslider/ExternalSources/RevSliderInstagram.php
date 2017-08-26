<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

/**
 * Instagram
 *
 * with help of the API this class delivers all kind of Images from instagram
 *
 * @package    socialstreams
 * @subpackage socialstreams/instagram
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderInstagram {

    protected $_framework;

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    Instagram API key
	 */
	private $api_key;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
   * Transient seconds
   *
   * @since    1.0.0
   * @access   private
   * @var      number    $transient Transient time in seconds
   */
  private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Instagram API key.
	 */
	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        $api_key,
        $transient_sec=1200
    ) {
        $this->_framework = $framework;

		$this->api_key = $api_key;
		$this->transient_sec = $transient_sec;
	}

	/**
     * Get Instagram Pictures Public by User
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	Instagram User id (not name)
	 */
	public function get_public_photos($search_user_id,$count){
		//call the API and decode the response
		$url = "https://www.instagram.com/".$search_user_id."/media/";

		$transient_name = 'revslider_' . md5($url);
		if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
			return ($data);

		$rsp = json_decode($this->_framework->wp_remote_fopen($url));

		for($i=0;$i<$count;$i++) {
			$return[] = $rsp->items[$i];
		}

		if(isset($rsp->items)){
		  $rsp->items = $return;
		  $this->_framework->set_transient( $transient_name, $rsp->items, $this->transient_sec );
		  return $rsp->items;
		}
		else return '';
	}

    /**
     * Get Instagram Pictures Public by Tag
     *
     * @since    1.0.0
     * @param    string    $user_id     Instagram User id (not name)
     */
    public function get_tag_photos($search_tag,$count){
        //call the API and decode the response
        $url = "https://www.instagram.com/explore/tags/".$search_tag."/?__a=1";

        $transient_name = 'revslider_' . md5($url);

        $rsp = json_decode($this->_framework->wp_remote_fopen($url));

        for($i=0;$i<$count;$i++) {
            $return[] = $rsp->tag->media->nodes[$i];
        }

        if(isset($rsp->tag->media->nodes)){
            $rsp->tag->media->nodes = $return;
            $this->_framework->set_transient( $transient_name, $rsp->tag->media->nodes, $this->transient_sec );
            return $rsp->tag->media->nodes;
        }
        else return '';
    }
}