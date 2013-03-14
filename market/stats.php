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

$type         = Params::getParam('type_stat');

?>
<?php
$item_id    = Params::getParam('itemId');
if($item_id != '') {
    $item_aux   = Item::newInstance()->findByPrimaryKey($item_id);
}
$type_stat  = Params::getParam('type_stat');
if($type_stat == '') {
    $type_stat = 'day';
}

$type       = Params::getParam('type');
if( $type == '') {
    $type = 'all';
}

$items = array();
$items = market_stats_all($type, $item_id);


$from_date = date('Y-m-d H:i:s');
if( Params::getParam('type_stat') == 'week' ) {
    $from_date = date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m"), date("d") - 70, date("Y")) );
} else if( Params::getParam('type_stat') == 'month' ) {
    $from_date = date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m") - 10, date("d"), date("Y")) );
} else {
    $from_date = date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m"), date("d") - 10, date("Y")) );
}


// all -> plugin top 10 , themes top 10
if( $type == 'all' ) {
    $aTopPlugins   = ModelMarket::newInstance()->getTop($from_date, 'plugins');
    $aTopThemes    = ModelMarket::newInstance()->getTop($from_date, 'themes');
    $aTopLanguages = ModelMarket::newInstance()->getTop($from_date, 'languages');

} else if( $type == 'plugins') {
    $aTopPlugins = ModelMarket::newInstance()->getTop($from_date, $type);
} else if( $type == 'themes') {
    $aTopThemes  = ModelMarket::newInstance()->getTop($from_date, $type);
} else if( $type == 'languages') {
    $aTopLanguages = ModelMarket::newInstance()->getTop($from_date, $type);
}

function market_stats_all($type, $item_id)
{
    $items = array();
    if( Params::getParam('type_stat') == 'week' ) {
        $stats_items = ModelMarket::newInstance()->getAllStats(date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m"), date("d") - 70, date("Y")) ),'week',$type, $item_id) ;
        for($k = 10; $k >= 0; $k--) {
            $items[date( 'W', mktime(0, 0, 0, date("m"), date("d") - ($k*7), date("Y")) ).'-'.date( 'Y', mktime(0, 0, 0, date("m"), date("d") - ($k*7), date("Y")) )] = 0 ;
        }
    } else if( Params::getParam('type_stat') == 'month' ) {
        $stats_items = ModelMarket::newInstance()->getAllStats(date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m") - 10, date("d"), date("Y")) ),'month',$type, $item_id) ;
        for($k = 10; $k >= 0; $k--) {
            $items[date( 'F', mktime(0, 0, 0, date("m") - $k, date("d"), date("Y")) )] = 0 ;
        }
    } else {
        $stats_items = ModelMarket::newInstance()->getAllStats(date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m"), date("d") - 10, date("Y")) ),'day',$type, $item_id) ;
        for($k = 10; $k >= 0; $k--) {
            $items[date( 'Y-m-d', mktime(0, 0, 0, date("m"), date("d") - $k, date("Y")) )] = 0 ;
        }
    }

    $max = 0 ;
    foreach($stats_items as $item) {
        $aux_array['num']           = $item['num'] ;
        $aux_array['ocadmin_num']   = $item['ocadmin_num'] ;
        $aux_array['d_date']        = $item['d_date'];
        if(Params::getParam('type_stat')=='week') {
            $aux_array['d_date'] .= ' '._('(start at )').' '.date('Y-M-d', strtotime($item['date_time']));
        }
        $items[$item['d_date']] = $aux_array;
        if( $item['num'] > $max ) {
            $max = $item['num'] ;
        }
    }

    return $items;
}
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        // Load the Visualization API and the piechart package.
        google.load('visualization', '1', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {
            /* ITEMS */
            var data = new google.visualization.DataTable();
            data.addColumn('string', '<?php echo osc_esc_js(__('Date')); ?>');
            data.addColumn('number', '<?php echo osc_esc_js(__('Total downloads')); ?>');
            data.addColumn('number', '<?php echo osc_esc_js(__('oc-admin downloads')); ?>');

            <?php
            $acomulate = 0 ;
            $i = 0 ;
            echo "data.addRows(" . count($items) . ");" ;

            foreach($items as $k => $aux) {
                $total = $aux['num'];
                if(!is_numeric($total))
                    $total = 0;

                $ocadmin = $aux['ocadmin_num'];
                if(!is_numeric($ocadmin))
                    $ocadmin = 0;

                echo "data.setValue(" . $i . ', 0, "' . $aux['d_date']. '");';
                echo "data.setValue(" . $i . ", 1, " . $total . ");";
                echo "data.setValue(" . $i . ", 2, " . $ocadmin . ");";
                $i++;

                $acomulate += $total;
            }
            ?>

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.AreaChart(document.getElementById('placeholder'));
            chart.draw(data, {
            title : '<?php _e('Market downloads', 'market'); ?>',
            focusTarget: 'category',
            pointSize: 6,
            chartArea:{
                left:10,
                top:10,
                height:"90%"
            },
            vAxis: {
                gridlines:{
                    color: '#DDD',
                    count: 4,
                    style: 'dooted'
                }},
            hAxis: {
                gridlines:{
                    color: '#333',
                    count: 3
                }}
          });
        }
    </script>


<div class="grid-system" id="stats-page">
    <div class="grid-row grid-50 no-bottom-margin">
        <div class="row-wrapper">
            <h2 class="render-title"><?php _e('Market Statistics', 'market'); ?> <?php if($item_id != '') { echo ' - '.@$item_aux['locale'][osc_admin_language()]['s_title']; } ?></h2>
        </div>
    </div>
    <div class="grid-row grid-50 no-bottom-margin">
        <div class="row-wrapper">
            <a id="monthly" class="btn float-right <?php if($type_stat=='month') echo 'btn-green';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type_stat=month&type=<?php echo $type;?><?php if($item_id != '') { echo '&itemId='.$item_id;}?>"><?php _e('Last 10 months', 'market') ; ?></a>
            <a id="weekly"  class="btn float-right <?php if($type_stat=='week') echo 'btn-green';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type_stat=week&type=<?php echo $type;?><?php if($item_id != '') { echo '&itemId='.$item_id;}?>"><?php _e('Last 10 weeks', 'market') ; ?></a>
            <a id="daily"   class="btn float-right <?php if($type_stat==''||$type_stat=='day') echo 'btn-green';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type_stat=day&type=<?php echo $type;?><?php if($item_id != '') { echo '&itemId='.$item_id;}?>"><?php _e('Last 10 days', 'market') ; ?></a>
            <?php if($item_id != '') {?>
            <a id="stats"   class="btn float-right btn-red" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php')?>"><?php _e('View all stats', 'market') ; ?></a>
            <?php } ?>
        </div>
    </div>
    <div class="grid-row grid-50 clear">
        <div class="row-wrapper">
            <?php if($item_id == '') { ?>
            <a id="all" class="btn <?php if($type=='all') echo 'btn-blue';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type=all&type_stat=<?php echo $type_stat;?>"><?php _e('All', 'market') ; ?></a>
            <a id="plugins" class="btn <?php if($type=='plugins') echo 'btn-blue';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type=plugins&type_stat=<?php echo $type_stat;?>"><?php _e('Plugins', 'market') ; ?></a>
            <a id="themes"  class="btn <?php if($type=='themes') echo 'btn-blue';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type=themes&type_stat=<?php echo $type_stat;?>"><?php _e('Themes', 'market') ; ?></a>
            <a id="languages"   class="btn <?php if($type=='languages') echo 'btn-blue';?>" href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php');?>?type=languages&type_stat=<?php echo $type_stat;?>"><?php _e('Languages', 'market') ; ?></a>
            <?php } ?>
        </div>
    </div>

    <div class="grid-row grid-100 clear">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Downloads', 'market'); ?> - <b><?php echo $acomulate;?></b></h3>
                </div>
                <div class="widget-box-content" style="height: 220px;overflow-y: auto;">
                    <b class="stats-title"><?php _e('Top 10'); ?></b>
                    <div id="placeholder" class="graph-placeholder" style="height:150px">
                        <?php if( count($items) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <?php if($type=='all' || $type=='plugins') { ?>
     <div class="grid-row grid-50 clear">
         <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Top 10 Plugins', 'market'); ?></h3>
                </div>
                <div class="widget-box-content" style="height: 220px;overflow-y: auto;">
                    <div id="placeholder" style="height:150px">
                        <table class="table" cellpadding="0" cellspacing="0">
                        <tbody>
                        <?php foreach( $aTopPlugins as $auxPlugin) { ?>
                            <?php View::newInstance()->_exportVariableToView('item', $auxPlugin) ; ?>
                            <tr>
                                <td class="children-cat"><a href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php').'?itemId='.osc_item_id(); ?>"><?php echo osc_item_title();?></a></td>
                                <td><?php echo $auxPlugin['total'];?></td>
                            </tr>
                        <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if($type=='all' || $type=='themes') { ?>
    <div class="grid-row grid-50">
         <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Top 10 Themes', 'market'); ?></h3>
                </div>
                <div class="widget-box-content" style="height: 220px;overflow-y: auto;">
                    <div id="placeholder" style="height:150px">
                        <table class="table" cellpadding="0" cellspacing="0">
                        <tbody>
                        <?php foreach( $aTopThemes as $auxTheme) { ?>
                            <?php View::newInstance()->_exportVariableToView('item', $auxTheme) ; ?>
                            <tr>
                                <td class="children-cat"><a href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php').'?itemId='.osc_item_id(); ?>"><?php echo osc_item_title();?></a></td>
                                <td><?php echo $auxTheme['total'];?></td>
                            </tr>
                        <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if($type=='all' || $type=='languages') { ?>
     <div class="grid-row grid-50 <?php if($type=='all') echo "clear"; ?>">
         <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Top 10 Languages', 'market'); ?></h3>
                </div>
                <div class="widget-box-content" style="height: 220px;overflow-y: auto;">
                    <div id="placeholder" style="height:150px">
                        <table class="table" cellpadding="0" cellspacing="0">
                        <tbody>
                        <?php foreach( $aTopLanguages as $auxLanguage) { ?>
                            <?php View::newInstance()->_exportVariableToView('item', $auxLanguage) ; ?>
                            <tr>
                                <td class="children-cat"><a href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php').'?itemId='.osc_item_id(); ?>"><?php echo osc_item_title();?></a></td>
                                <td><?php echo $auxLanguage['total'];?></td>
                            </tr>
                        <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>