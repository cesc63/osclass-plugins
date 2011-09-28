<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */
    $sQuery = __("ie. PHP Programmer", 'modern');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php osc_current_web_theme_path('head.php') ; ?>
        <meta name="robots" content="index, follow" />
        <meta name="googlebot" content="index, follow" />
        <script type="text/javascript">
            var sQuery = '<?php echo $sQuery; ?>' ;
            $('div#search_form').live('pageshow', function(){

                if($('input[name=sPattern]').val() == sQuery) {
                    $('input[name=sPattern]').css('color', 'gray');
                }
                $('input[name=sPattern]').click(function(){
                    if($('input[name=sPattern]').val() == sQuery) {
                        $('input[name=sPattern]').val('');
                        $('input[name=sPattern]').css('color', '');
                    }
                });
                $('input[name=sPattern]').blur(function(){
                    if($('input[name=sPattern]').val() == '') {
                        $('input[name=sPattern]').val(sQuery);
                        $('input[name=sPattern]').css('color', 'gray');
                    }
                });
                $('input[name=sPattern]').keypress(function(){
                    $('input[name=sPattern]').css('background','');
                })
            });
            function doSearch() {
                if($('input[name=sPattern]').val() == sQuery){
                    return false;
                }
                if($('input[name=sPattern]').val().length < 3) {
                    $('input[name=sPattern]').css('background', '#FFC6C6');
                    return false;
                }
                return true;
            }
        </script> 
    </head>
    <body>
        <div data-role="page" data-title="<?php echo osc_page_title() ; ?>">
            <div data-role="header" data-position="inline">
                <h1><?php echo osc_page_title() ; ?></h1>
                <a data-icon="search" data-iconpos="notext" data-transition="pop" data-rel="dialog" href="#search_form"></a>
                <div data-role="navbar" >
                    <ul>
                        <li>
                            <?php if( osc_is_web_user_logged_in() ) { ?>
                            <a href="<?php echo osc_user_logout_url() ; ?>"><?php _e('Logout', 'mobile') ; ?></a>
                            <?php } else {?>
                            <a href="<?php echo osc_user_login_url(); ?>"><?php _e('Log in','mobile')?></a>
                            <?php } ?>
                        </li>
                        <li>
                            <?php if(osc_user_registration_enabled()) { ?>
                            <a href="<?php echo osc_register_account_url() ; ?>"><?php _e('Register for a free account', 'mobile'); ?></a>
                            <?php }; ?>
                        </li>
                        <li>
                            <a data-icon="" href="<?php echo osc_item_post_url_in_category() ; ?>"><?php _e("Publish", 'mobile');?></a>
                        </li>
                    </ul>
                </div>
                <?php osc_show_flash_message() ; ?>
            </div><!-- /header -->

            <div data-role="content">
                
                <?php if(osc_count_categories () > 0) { ?>
                    <?php while ( osc_has_categories() ) { ?>
                        <div data-role="collapsible" data-collapsed="true">
                            <?php $cat_title =  osc_category_name() ; ?>
                            <?php $cat_link = str_replace(osc_base_url(), '', osc_search_category_url() ) ; ?>
                            <h3><span class="ui-li-count ui-btn-up-c ui-btn-corner-all left_count"><?php echo osc_category_total_items() ; ?></span><?php echo $cat_title ?></h3>
                            <ul data-role="listview" data-inset="true" data-theme="d">
                                <?php if ( osc_count_subcategories() > 0 ) { ?>
                                    <?php while ( osc_has_subcategories() ) { ?>
                                <li><a href="<?php echo str_replace(osc_base_url(), '', osc_search_category_url() ) ; ?>"><?php echo osc_category_name() ; ?><span class="ui-li-count"><?php echo osc_category_total_items() ; ?></span></a></li>
                                    <?php } ?>
                                <?php }  else {?>
                                        <li><a href="<?php echo $cat_link ; ?>"><?php echo $cat_title ; ?><span class="ui-li-count"><?php echo osc_category_total_items() ; ?></span></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                <?php }?>               
            </div><!-- /content -->

            <div data-role="footer">
                <?php osc_current_web_theme_path('footer.php') ; ?>
                <?php osc_run_hook('footer'); ?>
            </div><!-- /footer -->
            
        </div>
        
        <div data-role="dialog" id="search_form" data-title="<?php _e('Search','mobile');?>">
            <div data-role="header">
                <h1><?php echo osc_page_title() ; ?></h1>
                <a data-icon="back" data-iconpos="notext" data-rel="back"></a>
            </div>
            <div data-role="content">  
                <form action="<?php echo osc_base_url(true) ; ?>" method="get" class="search" onsubmit="javascript:return doSearch();">
                    <input type="hidden" name="page" value="search" />
                    <div data-role="fieldcontain">
                        <input data-theme="" type="search" name="sPattern" id="query" value="<?php echo ( osc_search_pattern() != '' ) ? osc_search_pattern() : $sQuery; ?>" />
                    <?php  if ( osc_count_categories() ) { ?>
                        <?php osc_categories_select('sCategory', null, __('Select a category', 'modern')) ; ?>
                    <?php  } ?>
                    <button type="submit"><?php _e('Search', 'modern') ; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>