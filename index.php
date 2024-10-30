<?php
/**
 * Plugin Name: Blogger Importer Wizard
 * Description:(BlogSpot to WordPress Migration Tool) Import Wizard for Blogspot  is a simple and effective plugin that allows you to easily import your blog posts from BlogSpot to WordPress. With a few clicks, you can transfer all of your content, including text, images, and formatting, to your WordPress site. Whether you're moving to WordPress permanently or just want to keep your content backed up, BlogSpot Importer makes it easy to transfer your posts with minimal effort.
 * Version: 2.2
 * Author: Siteskyline Plugins
 * Author URI: https://siteskyline.com/
 **/


add_action('admin_menu', 'BLIMWI_menu');

function BLIMWI_menu()
{

  add_menu_page(
    'Blogger Importer',
    // page <title>Title</title>
    'Blogger Importer',
    // link text
    'manage_options',
    // user capabilities
    'blogger_importer',
    // page slug
    'BLIMWI_page_callback',
    // this function prints the page content
    'dashicons-images-alt2',
    // icon (from Dashicons for example)
    4 // menu position
  );

  
  if ( !isset($_GET['page']) || 'blogger_importer' != $_GET['page']) {}else{
  wp_register_script('BLIMWI_rudr_top_lvl_menu', plugin_dir_url(__FILE__) . '/wp-insert.js', NULL, 1.0, true);


  wp_localize_script('BLIMWI_rudr_top_lvl_menu', 'additionalData', array(
    'nonce' => wp_create_nonce('wp_rest'),
    'siteURL' => site_url(),
    'ajax_url' => admin_url('admin-ajax.php'),
  )
  );

  wp_enqueue_script('BLIMWI_rudr_top_lvl_menu');
  }
  add_action('admin_init', 'register_BLIMWI_theme_settings');
}
function register_BLIMWI_theme_settings()
{
  //register our settings
  register_setting('BLIMWI-plugin-settings-group', 'BLIMWI_settings');
}


function BLIMWI_page_callback()
{
  
  ?>
  <style> 
.BLIMWI-container {
    background: #fff;
    padding: 1em;
    margin: 1em 0;
    border-radius: 15px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 200px;
    justify-content: center;
}
h2 {
    font-size: 2em;

  }
  button.BLIMWI-start:hover{
cursor: pointer;
  }
button.BLIMWI-start {
    background: #FF5722;
    color: #fff;
    border: none;
    border-radius: 500px;
    padding: 1em 3em;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap:10px
}
button.BLIMWI-start svg{
  fill:#fff;
  height: 18px;
  width:18px;
}
.BLIMWI-popup {
    position: fixed;
    top: 0;
    left: 0;
    background: #fff;
    width: 100%;
    height: 100%;
    display: none;
    align-items: center;
    align-content: center;
    justify-content: center;
    z-index: 0999999;
  
}
.BLIMWI-step1 {
    max-width: 800px;
    width: 100%;
}
.BLIMWI-step1 input {
    display: block;
    padding: 10px 2em;
    border-radius: 8px;
    font-size: 15px;
   
    width: 100%;
    margin: auto;
}button#import-button {
    background: #2271b1;
    border: none;
    color: #fff;
    padding: 10px 20px;
    border-radius: 4px;
    margin-top: 10px;
    font-size: 18px;
}
.BLIMWI-popup svg {
    position: absolute;
    top: 20px;
    right: 20px;
    height: 50px;
    width: 50px;
  
}
.BLIMWI-progress-bar {
    margin-top: 10px;
    background: #eee;
    padding: 5px;
    border-radius: 500px;
    display:none;
    position: relative;
}
.BLIMWI-progress {
    background: #00800045;
    position: absolute;
    left: 0;
    top: 0;
    width: 2%;
    height: 100%;
    border-radius: 500px;
}
.BLIMWI-progress-log {
    display: none;
    flex-direction: column;
    align-items: flex-start;
    max-height: 150px;
    overflow-y: scroll;
    background: #eee;
    padding: 1em;
    margin-top: 15px;
    margin-bottom: 15px;
}
/* HTML: <div class="loader"></div> */
.loader {
    width: 12px;
    aspect-ratio: 1;
    --c: no-repeat linear-gradient(#fff 0 0);
    background: var(--c) 0% 50%, var(--c) 50% 50%, var(--c) 100% 50%;
    background-size: 20% 100%;
    animation: l1 1s infinite linear;
    display: inline-block;
}
button#import-button > span:last-child {
    display: none;
}
.importing svg {
    height: 20px!important;
    width: 20px!important;
}
.importing span:last-child {
    display: block!important;
}
.importing span:first-child {
    display: none;
}
@keyframes l1 {
  0%  {background-size: 20% 100%,20% 100%,20% 100%}
  33% {background-size: 20% 10% ,20% 100%,20% 100%}
  50% {background-size: 20% 100%,20% 10% ,20% 100%}
  66% {background-size: 20% 100%,20% 100%,20% 10% }
  100%{background-size: 20% 100%,20% 100%,20% 100%}
}
button#step2-button {
    padding: 10px 20px;
    font-size: 18px;
    border: 0 ba;
    background: #FF5722;
    color: #fff;
    border: none;
    border-radius: 5px;
    display: none;
}
.BLIMWI-step2{
  display:none
}.BLIMWI-step3 {
    display: none;
}
p.submit{
  text-align:unset!important
}

.BLIMWI-form {
    display: grid;
    grid-template-columns: 5fr 1fr;
    grid-gap: 10px;
}
select#import-type {
    border-radius: 8px;
}
.BLIMWI-step2 #submit {
    padding: 10px 20px;
    font-size: 18px;
}
a.BLIMWI-rate {
    background: #FF5722;
    padding: 10px 20px;
    font-size: 18px;
    display: inline-block;
    text-decoration: none;
    color: #fff;
    border-radius: 8px;
}



</style>
  <div class="BLIMWI-container">
<h2>Start Importing Blogger Post to Wordpress in 1-Click</h2>
<button class='BLIMWI-start' onclick='BLIMWI_step1();'>Start Import Wizard <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M7.33 24l-2.83-2.829 9.339-9.175-9.339-9.167 2.83-2.829 12.17 11.996z"/></svg></button>
<div class='BLIMWI-popup'>
<svg onclick='close_BLIMWI_popup()' clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m12 10.93 5.719-5.72c.146-.146.339-.219.531-.219.404 0 .75.324.75.749 0 .193-.073.385-.219.532l-5.72 5.719 5.719 5.719c.147.147.22.339.22.531 0 .427-.349.75-.75.75-.192 0-.385-.073-.531-.219l-5.719-5.719-5.719 5.719c-.146.146-.339.219-.531.219-.401 0-.75-.323-.75-.75 0-.192.073-.384.22-.531l5.719-5.719-5.72-5.719c-.146-.147-.219-.339-.219-.532 0-.425.346-.749.75-.749.192 0 .385.073.531.219z"/></svg>
    <div class='BLIMWI-step1'>
      <div class='BLIMWI-form'>
      <input type='url' placeholder="Enter Your Blog URL" id='BLIMWI-blog-url'>
      <select id='import-type'>
      <option value='pages'>Page</option>
      <option value='posts'>Post</option>
      <option value='all' selected>Both</option>  
      <select>
</div>
      <button id='import-button' onclick="BLIMWI_start()"><span>Start import</span><span><div class="loader"></div> Importing</span></button>
      <button id='step2-button' onclick="BLIMWI_step2()">Continue </button>
      <div class="BLIMWI-progress-bar">
      <div class="BLIMWI-progress-text">Importing Posts...</div>
      <div class="BLIMWI-progress"></div></div>
      <div class="BLIMWI-progress-log"></div>
    </div>
    <div class="BLIMWI-step2">
    <?php $options = get_option('BLIMWI_settings'); ?>
    <form method="post" action="options.php">
        <?php settings_fields('BLIMWI-plugin-settings-group'); ?>
        <?php do_settings_sections('BLIMWI-plugin-settings-group'); ?>
        <h2>SEO Setting</h2>
        <p>This Will redirect all Posts From <b>domain.com/year/month/post.html</b> to <b>domain.com/post/</b> OR <b>domain.com/p/page.html</b> to <b>domain.com/page</b> and Make them SEO friendly. redirect will disappear if you uninstall plugin </p>
        <label for="blogger-importer-enable">Enable URL Redirects:</label>
        <input type="checkbox" name="BLIMWI_settings" value="1" <?php if (isset($options) && $options == 1)
          echo 'checked="checked"'; ?>>
      
        <?php submit_button(); ?>
      </form>
    </div>
    <div class="BLIMWI-step3">

    <h2>Congratulations! Your Blogger data has been migrated successfully.</h2>
<h3>How would you like to rate our plugin?</h3>

<a class='BLIMWI-rate' href='https://wordpress.org/support/plugin/import-wizard-blogspot/reviews/#new-post'>Rate Us</a>
    </div>
  
  
      
</div>

</div>
<form method="post" action="options.php">
        <?php settings_fields('BLIMWI-plugin-settings-group'); ?>
        <?php do_settings_sections('BLIMWI-plugin-settings-group'); ?>
        <h2>SEO Setting</h2>
        <p>This Will redirect all Posts From <b>domain.com/year/month/post.html</b> to <b>domain.com/post/</b> OR <b>domain.com/p/page.html</b> to <b>domain.com/page</b> and Make them SEO friendly. redirect will disappear if you uninstall plugin </p>
        <label for="blogger-importer-enable">Enable URL Redirects:</label>
        <input type="checkbox" name="BLIMWI_settings" value="1" <?php if (isset($options) && $options == 1)
          echo 'checked="checked"'; ?>>
      
        <?php submit_button(); ?>
      </form>

   
<script>
  function BLIMWI_step1(){
    document.querySelector('.BLIMWI-popup').style.display='flex';
  }
  function BLIMWI_step2(){
    document.querySelector('.BLIMWI-step1').style.display='none';
    document.querySelector('.BLIMWI-step2').style.display='block';
  }
  function close_BLIMWI_popup() {
   
        document.querySelector('.BLIMWI-popup').style.display='none';
        document.querySelector('.BLIMWI-step1').style.display='block';
        document.querySelector('.BLIMWI-step2').style.display='none';
        document.querySelector('.BLIMWI-step3').style.display='none';
    
}

  </script>
<script>
if (window.location.href.includes('#congrats')) {
 
  document.querySelector('.BLIMWI-popup').style.display='flex';
  document.querySelector('.BLIMWI-step1').style.display='none';
    document.querySelector('.BLIMWI-step2').style.display='none';
    document.querySelector('.BLIMWI-step3').style.display='block';
   

}

  </script>

  


  <?php
add_action('admin_footer', 'BLIMWI_redirect_after_submit');

function BLIMWI_redirect_after_submit() {
  if (isset($_GET['page']) && $_GET['page'] == 'blogger_importer') {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
      echo '<script type="text/javascript">
              window.location = "' . admin_url('admin.php?page=blogger_importer#congrats') . '";
            </script>';
    }
  }
}


}
function redirect_html_pages()
{
  
  $redirect_enabled = get_option( "BLIMWI_settings", false);
  if ($redirect_enabled) {
    $protocol = is_ssl() ? 'https' : 'http';
    if (preg_match('/^\/p\/(.+)\.html$/', $_SERVER['REQUEST_URI'], $matches)) {
      $new_url = home_url($matches[1] . '/', $protocol);
      wp_redirect($new_url, 301);
      exit();
    }
    if (preg_match('/^\/\d{4}\/\d{2}\/(.+)\.html$/', $_SERVER['REQUEST_URI'], $matches)) {

      $new_url = home_url($matches[1] . '/', $protocol);
      wp_redirect($new_url, 301);
      exit();
    }
  }
}
add_action('template_redirect', 'redirect_html_pages');