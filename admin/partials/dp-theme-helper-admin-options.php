<div class="heading"><h1 class="heading_content"><?php echo esc_html(get_admin_page_title());; ?></h1></div><div class="settings"><div class="setting__content"><div class="setting-section setting-section--vars"><div class="setting-section__content"><form class="setting-form" action="options.php" method="post"><?php settings_fields($this->plugin_name);
foreach($this->text_sections as $text_section){
    settings_fields($text_section);
}
do_settings_sections($this->plugin_name);
submit_button(); ?></form></div></div></div></div>