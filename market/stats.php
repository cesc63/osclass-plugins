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

function market_stats_all($type, $item_id) 
{ 
    $items = array();
    if( Params::getParam('type_stat') == 'week' ) {
        $stats_items = ModelMarket::newInstance()->getAllStats(date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m"), date("d") - 70, date("Y")) ),'week',$type, $item_id) ;
        for($k = 10; $k >= 0; $k--) {
            $items[date( 'W', mktime(0, 0, 0, date("m"), date("d"), date("Y")) ) - $k] = 0 ;
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
        $items[$item['d_date']] = $item['num'] ;
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
            var data2 = new google.visualization.DataTable();
            data.addColumn('string', '<?php echo osc_esc_js(__('Date')); ?>');
            data.addColumn('number', '<?php echo osc_esc_js(__('Items')); ?>');

            <?php /*ITEMS */
            $acomulate = 0 ;
            $k = 0 ;
            echo "data.addRows(" . count($items) . ");" ;
            foreach($items as $date => $num) {
                $acomulate += $num;
                echo "data.setValue(" . $k . ', 0, "' . $date . '");';
                echo "data.setValue(" . $k . ", 1, " . $num . ");";
                $k++ ;
            }
            ?>

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.AreaChart(document.getElementById('placeholder'));
            chart.draw(data, {
                colors:['#058dc7','#e6f4fa'],
                    areaOpacity: 0.1,
                    lineWidth:3,
                    hAxis: {
                    gridlines:{
                        color: '#333',
                        count: 3
                    },
                    viewWindow:'explicit',
                    showTextEvery: 2,
                    slantedText: false,
                    textStyle:{
                        color: '#058dc7',
                        fontSize: 10
                    }
                    },
                    vAxis: {
                        gridlines:{
                            color: '#DDD',
                            count: 4,
                            style: 'dooted'
                        },
                        viewWindow:'explicit',
                        baselineColor:'#bababa'

                    },
                    pointSize: 6,
                    legend: 'none',
                    chartArea:{
                        left:10,
                        top:10,
                        width:"95%",
                        height:"80%"
                    }
                });
        }
    </script>
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
                    <h3><?php _e('New listing'); ?> - <?php _e('Total', 'market'); ?> <?php echo $acomulate; ?></h3>
                </div>
                <div class="widget-box-content">
                    <b class="stats-title"><?php _e('Number of new listings'); ?></b>
                    <div id="placeholder" class="graph-placeholder" style="height:150px">
                        <?php if( count($items) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>