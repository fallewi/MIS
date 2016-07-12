<?php
/**
 * BlueAcorn_ProductVideos extension
 *
 *
 * @category       BlueAcorn
 * @package        BlueAcorn_ProductVideos
 * @author         Blue Acorn, Inc. <code@blueacorn.com>
 * @copyright      Copyright © 2014 Blue Acorn, Inc.
 */
/**
 * Product helper
 *
 * @category    BlueAcorn
 * @package     BlueAcorn_ProductVideos
 *
 */
class BlueAcorn_ProductVideos_Helper_Product extends BlueAcorn_ProductVideos_Helper_Data {

    const VIMEO = 'vimeo';
    const YOUTUBE = 'youtube';
    const VIMEO_IFRAME = '<iframe id="video-player" src="http://player.vimeo.com/video/VIDEO_ID?api=1" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
    const YOUTUBE_IFRAME = '<iframe id="video-player" src="http://www.youtube.com/embed/VIDEO_ID?SUGGESTED_VIDEOSfeature=player_detailpage" width="100%" height="100%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
    const VIMEO_XML_URL = 'http://vimeo.com/api/v2/video/VIDEO_ID.xml';
    const YOUTUBE_XML_URL = 'http://img.youtube.com/vi/VIDEO_ID/sddefault.jpg';
    const DEFAULT_DEFAULT_IMAGE = "default_default.png";


    /**
     * get the selected videos for a product
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     *
     */
    public function getSelectedVideos(Mage_Catalog_Model_Product $product){
        if (!$product->hasSelectedVideos()) {
            $videos = array();
            foreach ($this->getSelectedVideosCollection($product) as $video) {
                $videos[] = $video;
            }
            $product->setSelectedVideos($videos);
        }
        return $product->getData('selected_videos');
    }

    /**
     * get video collection for a product
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return BlueAcorn_ProductVideos_Model_Resource_Video_Collection
     *
     */
    public function getSelectedVideosCollection(Mage_Catalog_Model_Product $product){
        $collection = Mage::getResourceSingleton('blueacorn_productvideos/video_collection')
            ->addProductFilter($product);
        return $collection;
    }

    /**
     * grab video id from provided url
     * @access public
     * @param $videoObject
     * @return bool|mixed|string
     */
    public function getVideoId($videoObject){

        $this->checkAndHandleIfUrlIsEmbedCode($videoObject);
        $videoUrl= $videoObject->getData('url');

        $videoId = '';
        if (preg_match('%(?:' . self::YOUTUBE . '(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match)) {
            $this->videoType = self::YOUTUBE;
            $videoId = $match[1];
        } else {
            $this->videoType = self::VIMEO;
            $videoId = (preg_match('/[\/][0-9]+[?|"]?/', $videoUrl, $videoId)) ?
                str_replace(array('/', '?', '"'), array('', '', ''), $videoId[0]) : false;
        }
        return $videoId;
    }

    /**
     * Returns template ready iframe based on provided url. 
     * @param $videoObject
     * @return mixed
     */
    public function getVideoIFrameProper($videoObject){
        $videoId = $this->getVideoId($videoObject);
        $iframeVideoHtml = str_replace('VIDEO_ID', $videoId, ($this->videoType == self::YOUTUBE) ? self::YOUTUBE_IFRAME : self::VIMEO_IFRAME);

        // If 'use HTTPS' is enabled replace http with https for YouTube videos
        if(Mage::getStoreConfig('blueacorn_productvideos/video/use_https') && $this->videoType == self::YOUTUBE){
            $iframeVideoHtml = str_replace('http', 'https', $iframeVideoHtml);
        }

        // if show suggested videos is enabled remove SUGGESTED_VIDEOS else replace with url parameter that removes suggested videos for YouTube videos
        if (Mage::getStoreConfig('blueacorn_productvideos/video/show_suggested') && $this->videoType == self::YOUTUBE){
            $iframeVideoHtml = str_replace('SUGGESTED_VIDEOS', '', $iframeVideoHtml);
        } else {
            $iframeVideoHtml = str_replace('SUGGESTED_VIDEOS', 'rel=0&', $iframeVideoHtml);
        }

        // if privacy enhanced mode is enabled change url to www.youtube-nocookie.com for YouTube videos
        if (Mage::getStoreConfig('blueacorn_productvideos/video/privacy_enhanced') && $this->videoType == self::YOUTUBE){
            $iframeVideoHtml = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $iframeVideoHtml);
        }

        return $iframeVideoHtml;
    }

    /**
     * Returns video urls or path to uploaded video
     * @param $videoObject
     * @return mixed
     * @throws Exception
     */
    public function getVideo($videoObject){
        $this->checkAndHandleIfUrlIsEmbedCode($videoObject);
        $videoFile= $videoObject->getData('file');
        $videoUrl= $videoObject->getData('url');

        if(!is_null($videoUrl)){
            return $videoUrl;
        }
        else if(!is_null($videoFile)){
            return $videoFile;
        }
        else{
            throw new Exception('There is no video associated with this product.');
        }

    }

    /**
     * Return thumbnail image url location.
     * @param $videoObject
     * @return mixed
     *
     */
    public function getThumbnail($videoObject){

        return $videoObject->getData('thumbnail');

    }

    /**
     * This method checks if:
     *     the url is set -> get the thumbnail url then save the url's image to media and set the video object's 'thumbnail' to the image's name(hash).
     *     the file is set and thumbnail is not -> set video object's thumbnail to the default image set in the config.
     * @param $videoObject
     */
    public function setThumbnail($videoObject)
    {
        if($videoObject->getData('url') != '')
        {
            $thumbUrl = $this->buildRequest($videoObject);
            $thumbnailName = $this->saveVideoThumb($thumbUrl);
            $videoObject->setData('thumbnail', $thumbnailName);
        }
        elseif($videoObject->getData('file') !='')
        {
            if($videoObject->getData('thumbnail')=='' || $videoObject->getData('thumbnail') == Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl())
            {
                $thumbnailName= Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl() . DS . Mage::getStoreConfig('blueacorn_productvideos/video/thumb_default');
                $thumbnailName= str_replace(Mage::getBaseUrl('media'), '', $thumbnailName);

                if(!empty($thumbnailName) && $thumbnailName != str_replace(Mage::getBaseUrl('media'), '', Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl() . DS))
                {
                    $videoObject->setData('thumbnail', $thumbnailName );
                }
                else
                {
                    $videoObject->setData('thumbnail', '');
                }
            }
        }
        else
        {
            return;
        }

    }

    /**
     * Builds the thumbnail request url for the respective youtube or vimeo site's api.
     * @param $videoObject
     * @return bool|mixed
     */
    public function buildRequest($videoObject){

        $videoId = $this->getVideoId($videoObject);
        if($this->videoType == self::VIMEO){
            $requestUrl = str_replace('VIDEO_ID', $videoId, self::VIMEO_XML_URL);

            $client = new Varien_Http_Client();
            $client->setUri($requestUrl);

            $thumbUrl = false;
            try {
                $response = $client->request();
                $xmlResponse = new SimpleXMLElement($response->getBody());
                $thumbUrl = (string) $xmlResponse->video->thumbnail_medium;

            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        } else {
            $thumbUrl = str_replace('VIDEO_ID', $videoId, self::YOUTUBE_XML_URL);
        }
        return $thumbUrl;
    }

    /**
     * Saves the thumbnail.
     * @param $thumbUrl
     * @return bool|mixed|string
     */
    protected function saveVideoThumb($thumbUrl){
        $destination = Mage::helper('blueacorn_productvideos/video')->getThumbBaseDir(). DS . md5($thumbUrl) . '.' . pathinfo($thumbUrl, PATHINFO_EXTENSION);
        try{
            $client = new Varien_Http_Client($thumbUrl);
            $body = $client->request('GET')->getBody();
            if(!@file_put_contents($destination, $body)){
                throw new Exception('File could not be downloaded');
            }
        } catch (Exception $e){
            Mage::log($e->getMessage());
            return false;
        }
        $destination = str_replace(Mage::getBaseDir('media') . DS,'',$destination);

        return $destination;
    }

    /**
     * Gets the title of the video, trimmed and all whitespace replaced with a '_'.
     *     This also appends the $appendedName if inputted.
     * @param $videoObject
     * @param null $appendedName
     * @return null|string
     */
    public function getFormattedVideoTitle($videoObject, $appendedName = null)
    {
        $videoTitle = $videoObject->getData('title');
        $formattedVideoTitle = str_replace(' ', '_', trim($videoTitle));
        return $formattedVideoTitle . (($appendedName !== null)? '_' . $appendedName : $appendedName);
    }

    /**
     * Check if the user inputted a url or embed code in for the product video url.
     *      If true, it will handle grabbing the src from the code and updating the videoObject.
     * @param $videoObject
     */
    protected function checkAndHandleIfUrlIsEmbedCode($videoObject)
    {
        if($videoObject->getData('url') != false)
        {
            $embStr = $videoObject->getData('url');
            if(strpos($embStr, 'src=') !== false)
            {
                //grabs the src attr. from the embed code.
                preg_match_all('/src="[^"]+?\/([^\/"]+)"/', $embStr, $srcValueArray);
                if (isset($srcValueArray[0][0])) {

                    //grabs everything between the quotation marks.
                    preg_match_all('/"(.*)"/', $srcValueArray[0][0], $urlArray);

                    //     use something like the below function to grab the url w/o the paramaters...
                    //    $strFinal = substr($str, 0, strpos($str, 'non-alpha-numeric')+1)

                    if (isset($urlArray[1][0]))
                    {
                        $videoObject->setUrl($urlArray[1][0]);
                    }
                }

            }
        }
    }

    /**
     * Grab the default_default.png image that was included in the MSR module.
    This method is ONLY used if the uploaded video:
    -has no thumbnail uploaded to it.
    -there is no default image set in the system/config menu.
     * @return string
     */
    public function getDefaultDefaultImage()
    {
        return Mage::helper('blueacorn_productvideos/video')->getThumbBaseUrl() . DS . self::DEFAULT_DEFAULT_IMAGE;
    }
}
