<?php


/**
 * Turn banner images into featured media.
 * 
 */
function invw_banner_hero( $hero, $post, $classes ) {

	// Return if a hero is set or this isn't single.
	if( !empty($hero) || !is_single() ) {
		return $hero;
	}

	$firstBanner;
	$afterBannerNode;
	$doc;

	$bannerIndex = _invw_find_banner($post->post_content,$firstBanner,$afterBannerNode,$doc);

	if(empty($firstBanner)) {
		return $hero;
	}

	// Creates the array:
	// 		$matches[0] = <img src="..." class="..." id="..." />
	//		$matches[1] = value of src.

	$pattern = '/<img\s+[^>]*class="([^"]*)"[^>]*src="([^"]*)"[^>]*>/';
	$hasImg = preg_match($pattern,$firstBanner->ownerDocument->saveHTML($firstBanner),$matches);

	// 3: if there's no image, there's nothing to worry about.

	if( !$hasImg )
		return $hero;

	$imgDom = $matches[0];
	$imgClasses = $matches[1];
	$src = $matches[2];

	// Get the credit text
	
	$pattern = '/<h6>(.*?)<\/h6>/';

	$hasCredit = $afterBannerNode->nodeName == 'h6';
	$caption = $hasCredit ? $afterBannerNode->textContent : '';

	$thumb_meta = array(
			'caption' => $caption
		);

	$classes .= " hero is-image";

	// Build the dom.
	ob_start();

	echo "<div class='$classes'>"; ?>

	<img width="1170" src="<?php echo $src; ?>" class="attachment-full" alt="<?php echo $thumb_meta['caption']; ?>">

	<?php
	if (!empty($thumb_meta)) {
		if (!empty($thumb_meta['credit'])) { ?>
			<p class="wp-media-credit"><?php echo $thumb_meta['credit'];
				if (!empty($thumb_meta['organization'])) { ?>/<?php echo $thumb_meta['organization']; } ?></p>
		<?php }
	
		if (!empty($thumb_meta['caption'])) { ?>
			<p class="wp-caption-text"><?php echo $thumb_meta['caption']; ?></p>
		<?php }
	} ?>
	</div>
	<?php

	$ret = ob_get_clean();
	
	return $ret;


}
add_filter('largo_get_hero','invw_banner_hero',10,3);

/**
 * Filter the content to remove the a banner from the post if it's been promoted
 * to the top hero.
 * 
 */
function invw_remove_banner($content) {
	
	global $post;

	$firstBanner;
	$afterBannerNode;
	$doc;

	$bannerIndex = _invw_find_banner($content,$firstBanner,$afterBannerNode,$doc);

	if(empty($firstBanner)) {
		return $content;
	}

	$pattern = '/<img\s+[^>]*class="([^"]*)"[^>]*src="([^"]*)"[^>]*>/';
	$hasImg = preg_match($pattern,$firstBanner->ownerDocument->saveHTML($firstBanner),$matches);

	$hasCredit = $afterBannerNode->nodeName == 'h6';

	// Remove the image elements.
	if($hasImg && $bannerIndex == 0) {
		$firstBanner->parentNode->removeChild($firstBanner);
		if($hasCredit) {
			$afterBannerNode->parentNode->removeChild($afterBannerNode);
		}
	} else {
		return $content;
	}

	return $doc->saveHTMLExact();


}
add_filter('the_content','invw_remove_banner',10,1);

/**
 * Returns DOMElements for the top banner and caption in the post.
 * 
 * 
 */
function _invw_find_banner($content,&$bannerNode,&$creditNode,&$doc = null) {

	// We can't count on this always being installed.
	if (!class_exists('DOMDocument') || !is_single()) {
		$bannerNode = null;
		$creditNode = null;
		$doc = null;
		return null;
	}

	// make our content a little more reliable to parse
	$content = wpautop($content);

	$doc = new SmartDOMDocument();
	$doc->loadHTML($content);
	$doc->encoding = 'UTF-8';

	try {
		$grafs = $doc->documentElement->childNodes->item(0)->childNodes;
	} catch (Exception $e) {
		$bannerNode = null;
		$creditNode = null;
		$doc = null;
		return null;
	}

	$bannerNode = null;
	$creditNode = null;
	$bannerIndex = null;

	foreach($grafs as $index=>$p) {

		if(empty($bannerNode) && get_class($p) == "DOMElement") {
			
			// find each image nested in the DOMElement.
			$imgs = $p->getElementsByTagName('img');
			foreach($imgs as $i) {
				
				// If this img has a banner class it's the one we want.
				if($i->hasAttribute("class") && strpos($i->getAttribute("class"),'banner') !== false) {

					$bannerNode = $i;
					$bannerIndex = $index;
					
					// Find the next node (maybe an <h6> with credit info).
					while(
						$grafs->item($index)->nextSibling != null && 
						get_class($grafs->item($index)->nextSibling) != "DOMElement" &&
						$index < 100) {
						$index++;
					}
					
					$creditNode = $grafs->item($index)->nextSibling;
					break;

				}
			}
		} 
	}

	return $bannerIndex;
}


/**
* This class overcomes a few common annoyances with the DOMDocument class,
* such as saving partial HTML without automatically adding extra tags
* and properly recognizing various encodings, specifically UTF-8.
*
* @author Artem Russakovskii
* @version 0.4.1
* @link http://beerpla.net
* @link http://www.php.net/manual/en/class.domdocument.php
*/
if(!class_exists("SmartDOMDocument")) {
  class SmartDOMDocument extends DOMDocument {

    /**
    * Adds an ability to use the SmartDOMDocument object as a string in a string context.
    * For example, echo "Here is the HTML: $dom";
    */
    public function __toString() {
      return $this->saveHTMLExact();
    }

    /**
    * Load HTML with a proper encoding fix/hack.
    * Borrowed from the link below.
    *
    * @link http://www.php.net/manual/en/domdocument.loadhtml.php
    *
    * @param string $html
    * @param string $encoding
    * 
    * @return bool
    */
    public function loadHTML($html, $encoding = "UTF-8") {
      $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
      return @parent::loadHTML($html); // suppress warnings
    }

    /**
    * Return HTML while stripping the annoying auto-added <html>, <body>, and doctype.
    *
    * @link http://php.net/manual/en/migration52.methods.php
    *
    * @return string
    */
    public function saveHTMLExact() {
      $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                                    "!</body></html>$!si"),
                              "",
                              $this->saveHTML());

      return $content;
    }
  }
}


