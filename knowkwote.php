<?
	/*
		Plugin Name: Random Quote from KnowKwote
		Plugin URI: http://knowkwote.com/about/wp-plugin
		Description: Random Quote plugin by KnowKwote.com. Displays a random quote in a widget, pulled from the KnowKwote servers. 
		Version: 0.2
		Author: Nicolas E Martin
		Author URI: http://knowkwote.com
		License: GPL2
	*/
	
	/*  Copyright 2012  Nicolas E. Martin  (email : admin@knowkwote.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
	
	add_action("widgets_init", array('KnowKwote', 'register'));
	register_activation_hook( __FILE__, array('KnowKwote', 'activate'));
	register_deactivation_hook( __FILE__, array('KnowKwote', 'deactivate'));
	
	class KnowKwote {
		function activate(){
			$data = array(
				'title' => 'Random Quote',
				'authorlink' => 'enable',
				'fontstyle' => 'inherit'
			);
			if ( ! get_option('KnowKwote')){
				add_option('KnowKwote' , $data);
			} else {
				update_option('KnowKwote' , $data);
			}
		}
		
		function control(){
			$data = get_option('KnowKwote');
			?>
				<p>
					<label>Widget Title:</label>
                    <input name="KnowKwote_title" type="text" value="<?php echo $data['title']; ?>" />
                </p>
				<p>
					<label>Font Style</label>
                    <select name="KnowKwote_fontstyle">
                    	<option value="inherit">inherit</option>
                        <option value="bold">bold</option>
                        <option value="italic">italic</option>
                        <option value="bolditalic">bold and italic</option>
                    </select>
                </p>
                <p>
                    <label>Author Link</label>
                </p>
                <p>
                    <select name="KnowKwote_authorlink">
                    	<option value="enable">Enable</option>
                        <option value="disable">Disable</option>
                    </select>
				</p>
                <p style='font-style:italic;'>Links the author to KnowKwote.com</p>
			<?
			if (isset($_POST['KnowKwote_title'])){
				$data['title'] = attribute_escape($_POST['KnowKwote_title']);
				$data['authorlink'] = attribute_escape($_POST['KnowKwote_authorlink']);
				$data['fontstyle'] = attribute_escape($_POST['KnowKwote_fontstyle']);
				update_option('KnowKwote', $data);
			}
		}
	
		function deactivate(){
			delete_option('KnowKwote');
		}
		function widget($args){
			$data = get_option('KnowKwote');
			echo $args['before_widget'];
			echo $args['before_title'] . $data['title'] . $args['after_title'];

			$site_url = site_url();

			$postdata = http_build_query(
				array(
					'authorlink' => $data['authorlink'],
					'fontstyle' => $data['fontstyle'],
					'site_url' => $site_url
				)
			);
			
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);
			
			$context  = stream_context_create($opts);
			
			$html = file_get_contents('http://knowkwote.com/wp-plugin/0_2/wp-interface.php', false, $context);
			
			echo $html;
		}

		function register(){
			register_sidebar_widget('KnowKwote Quotes', array('KnowKwote', 'widget'));
			register_widget_control('KnowKwote Quotes', array('KnowKwote', 'control'));
		}
	}

?>