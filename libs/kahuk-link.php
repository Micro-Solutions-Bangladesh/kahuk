<?php
if (!defined('KAHUKPATH')) {
	die();
}

/**
 * Initializes KahukLink Class
 *
 * @return KahukLink
 */
function kahuk_link_init( $url ) {
    return KahukLink::init( $url );
}

/**
 * 
 */
class KahukLink
{
    public $errors;

    public $rawUrl = '';
    public $linkImages = [];
	public $url_title = '';
	public $url_description = '';
    public $trackback = '';
    public $rawHtml = '';

    /**
     * Class construcotr
     */
    private function __construct( $url ) {
        $this->rawUrl = $url;

        $this->errors = new Kahuk_Error();
        $this->initializeAssets();
    }

    /**
     * Initializes a singleton instance
     *
     * @return self instance
     */
    public static function init( $url ) {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self( $url );
        }

        return $instance;
    }

    /**
     * 
     */
    function initializeAssets() {
        $url = esc_url( $this->rawUrl );

        if ( empty( $url ) ) {
            $this->errors->add( 'emptyurl', 'Empty URL!' );

            return;
        }


        $r = new KahukHTTPRequest( $url );
        $html = $r->DownloadToString();

        if ( ( "BADURL" == $html ) || empty( $html ) ) {
            $this->errors->add( 'invalidurl', 'Bad URL!' );

            return;
        }

        $this->rawHtml = $html;
        $this->url = $url;

        $this->set_link_title();

        $this->set_link_images();

        $this->set_link_description();

        $this->set_link_trackbacks();
    }

    /**
     * Detect trackbacks
     */
    function set_link_trackbacks() {
        $trackback = '';

        if ( isset($_POST['trackback'] ) && sanitize( $_POST['trackback'], 3 ) != '' ) {
			$trackback = trim(sanitize($_POST['trackback'], 3));

		} elseif (
			preg_match('/trackback:ping="([^"]+)"/i', $this->rawHtml, $matches) ||
			preg_match('/trackback:ping +rdf:resource="([^>]+)"/i', $this->rawHtml, $matches) ||
			preg_match('/<trackback:ping>([^<>]+)/i', $this->rawHtml, $matches)
		) {
			$trackback = trim($matches[1]);

		} elseif (preg_match('/<a[^>]+rel="trackback"[^>]*>/i', $this->rawHtml, $matches)) {
			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
				$trackback = trim($matches2[1]);
			}
		} elseif (preg_match('/<a[^>]+href=[^>]+>trackback<\/a>/i', $this->rawHtml, $matches)) {
			if (preg_match('/href="([^"]+)"/i', $matches[0], $matches2)) {
				$trackback = trim($matches2[1]);
			}
		}

        $this->trackback = $trackback;
    }


    /**
     * finding the description of the article to be used when submitting a story on Kahuk.
     */
    function set_link_description() {
        $url_description = '';

        if (preg_match("'<meta\s+name=[\"\']description[\"\']\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$url_description = $matches[1];
		} elseif (preg_match("'<meta\s+content=[\"\']([^<]*?)[\"\']\s+name=[\"\']description[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$url_description = $matches[1];
		} elseif (preg_match("'<meta\s+(?:(name|property)=[\"\'](og:description|twitter:description)[\"\']\s+)?(?:property=[\"\'](og:description|twitter:description)[\"\'])?(?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s?/?>'si", $this->rawHtml, $matches)) {
			$url_description = $matches[4];
		} elseif (preg_match("'<meta\s+(?:property=[\"\'](og:description|twitter:description)[\"\']\s+)?(?:(name|property)=[\"\'](og:description|twitter:description)[\"\'])?(?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s?/?>'si", $this->rawHtml, $matches)) {
			$url_description = $matches[4];
		} elseif (preg_match("'<meta\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?(?:property=[\"\'](og:description|twitter:description)[\"\']\s+)?(?:(name|property)=[\"\'](og:description|twitter:description)[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s?/?>'si", $this->rawHtml, $matches)) {
			$url_description = $matches[4];
		}

        $this->url_description = $url_description;
    }
    
    /**
     * finding the image of the article to be used when submitting a story on Kahuk.
     */
    function set_link_images() {
        $og_twitter_image = '';

        if (preg_match("'<meta\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s+content=[\"\']([^<]*?)[\"\'](?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$og_twitter_image = $matches[3];
		} elseif (preg_match("'<meta\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\'](?:\s+itemprop=[\"\'][^<]*?[\"\'])?\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$og_twitter_image = $matches[3];
		} elseif (preg_match("'<meta\s+content=[\"\']([^<]*?)[\"\']\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$og_twitter_image = $matches[1];
		} elseif (preg_match("'<meta\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?content=[\"\']([^<]*?)[\"\']\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$og_twitter_image = $matches[1];
		} elseif (preg_match("'<meta\s+(?:itemprop=[\"\'][^<]*?[\"\']\s+)?(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$og_twitter_image = $matches[3];
		} elseif (preg_match("'<meta\s+content=[\"\']([^<]*?)[\"\']\s+(name|property)=[\"\'](og:image|twitter:image|twitter:image:src)[\"\']\s+(?:itemprop=[\"\'][^<]*?[\"\'])?\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches)) {
			$og_twitter_image = $matches[1];
		}

        // $this->og_twitter_image = $og_twitter_image; // depricated
        $this->linkImages[0]['url'] = $og_twitter_image;
    }


    /**
     * 
     */
    function set_link_title() {
        $url_title = '';

        // Look for the title text in meta tag
        if ( preg_match( "'<meta\s+property=[\"\']og:title[\"\']\s+content=[\"\']([^<]*?)[\"\']\s{0,}\/?\s{0,}>'si", $this->rawHtml, $matches ) ) {
            $url_title = trim( $matches[1] );
        }

        // Look for the title text in the page title
        if ( empty( $url_title ) ) {
			if ( preg_match( '/<title>(.+?)<\/title>/si', $this->rawHtml, $matches ) ) {
                $url_title = trim( $matches[1] );
                $url_title = preg_replace( '/\|?-?~?[^|-~]*$/', '', $url_title );
            }
		}

        $this->url_title = sanitize_text_field( $url_title );
    }
}

