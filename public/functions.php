<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('get_social_url')) {

	/**
	 * Gets profile URL
	 */
	function cher_profile_url($id = null) {
		if ($id === null) return;
		$url = get_option('cher_' . $id . '_url');
		if ($url !== null) {
			return $url;
		} else {
			return false;
		}
	}
}

if (!function_exists('cher_profile_urls')) {

	/**
	 * Gets all URLs for all profiles, if set.
	 *
	 * @param array $ids Profile keys. Used to specify a different order of links.
	 * @return array Profile URLs
	 */
	function cher_profile_urls($ids = null) {
		$urls = [];

		if ($ids === null) {

			$ids = [
				'twitter',
				'facebook',
				'linkedin',
				'youtube',
				'vimeo',
				'instagram',
				'pinterest',
				'houzz',
			];
		}

		foreach ($ids as $id) {

			if ($url = cher_profile_url($id)) {
				$urls[$id] = $url;
			}
		}

		return $urls;
	}
}

if (!function_exists('cher_links')) {

	function cher_links($echo = true) {

		$cher_show_links = get_option('cher_show_links');

		if (empty($cher_show_links)) {
			return;
		}

		$html = '<ul class="cher-links">';

		global $post;
		$post = (object) $post;

		$title = html_entity_decode(get_the_title());
		$emailTitle = str_replace("&", "%26", $title);
		$excerpt = get_the_excerpt();
		$url = get_permalink($post->ID);
		$image_src = '';

		if (has_post_thumbnail()) {
			$image_src = get_the_post_thumbnail_url($post->ID, 'large');
		}

		$twitter = explode('/', get_option('cher_twitter_url'));
		$vai = array_pop($twitter);

		$share_schemes = array(
			'twitter' => array(
				'id' => 'twitter',
				'href_base' => 'https://twitter.com/intent/tweet/',
				'href_params' => array(
					'url' => $url,
                    'text' => $title,
			    	'via' => $vai,
				),
				'title' => 'Share on Twitter'
			),
			'facebook' => array(
				'id' => 'facebook',
				'href_base' => 'https://facebook.com/sharer.php',
				'href_params' => array(
					'u' => $url
				),
				'title' => 'Share on Facebook'
			),
			'messenger' => array(
				'id' => 'messenger',
				'href_base' => $url,
				'title' => 'Messenger'
			),
			'linkedin' => array(
				'id' => 'linkedin',
				'href_base' => 'https://www.linkedin.com/shareArticle',
				'href_params' => array(
					'mini' => 'true',
					'url' => $url,
					'title' => $title,
					'summary' => $excerpt,
					'source' => get_bloginfo('name'),
				),
				'title' => 'Share on LinkedIn'
			),
			'pinterest' => array(
				'id' => 'pinterest',
				'href_base' => 'http://pinterest.com/pin/create/button/',
				'href_params' => array(
					'url' => $url,
					'media' => $image_src,
					'description' => $title
				),
				'title' => 'Share on Pinterest'
			),
			'email' => array(
				'id' => 'email',
				'href_base' => 'mailto:',
				'href_params' => array(
					'subject' => $emailTitle,
					'body' => $title . '%0A' . $url
				),
				'title' => 'Share via Email'
			),
		);

		foreach ($cher_show_links as $link) {

			$profile = $share_schemes[$link];
			$share_id = $profile['id'];

			if ($share_id === 'email') {
				$share_url = $profile['href_base'] . '?';
				$share_url .= 'subject=' . $profile['href_params']['subject'];
				$share_url .= '&amp;body=' . $profile['href_params']['body'];
			} elseif ($share_id === 'messenger') {
				$share_url = $profile['href_base'];
			} else {
				$share_url = $profile['href_base'] . '?';
				$share_url .= http_build_query($profile['href_params']);
			}

			$share_title = $profile['title'];

			$html .= '<li class="cher-link-item cher-link-' . $share_id . '">';

			if ($share_id === 'messenger') {
				$html .= '<a id="cher-link-' . $share_id . '" class="cher-link" href="http://www.facebook.com/dialog/send?app_id=307316345962358&amp;link=' . $share_url . '&amp;redirect_uri=' . $share_url . '" title="' . esc_attr($share_title) . '" rel="nofollow,noopener"';
			} else {
				$html .= '<a id="cher-link-' . $share_id . '" class="cher-link" href="' . $share_url . '" title="' . esc_attr($share_title) . '" rel="nofollow,noopener"';
			}

			if ($share_id !== 'email') {
				$html .= ' target="_blank"';
			}

			$html .= '><i class="icon icon-' . $share_id . '" data-grunticon-embed></i><span class="cher-link-text">' . esc_html($share_title) . '</span>';
			$html .= "</a></li>";

		}

		$html .= '</ul>';

		if ($echo === true) {
			echo $html;
		} else {
			return $html;
		}
	}

	function cher_shortcode($content = null) {
		$output_string = cher_links(false);
		return force_balance_tags($output_string);
	}

	add_shortcode('cher-links', 'cher_shortcode');

}
