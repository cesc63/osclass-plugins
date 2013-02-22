<?php
    if(!osc_is_admin_user_logged_in()) {
        die;
    }

    // last 10 days
    $from_date = date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m"), date("d") , date("Y")-1) );

    $aType  = array(
        'all'       => __('All', 'market'),
        'plugins'   => __('Plugins', 'market'),
        'themes'    => __('Themes', 'market'),
        'languages' => __('Languages', 'market')
    );

    $aTop['all']        = ModelMarket::newInstance()->getTop($from_date);
    $aTop['plugins']    = ModelMarket::newInstance()->getTop($from_date, 'plugins');
    $aTop['themes']     = ModelMarket::newInstance()->getTop($from_date, 'themes');
    $aTop['languages']  = ModelMarket::newInstance()->getTop($from_date, 'languages');

    $items = array();
    $total_downloads    = ModelMarket::newInstance()->getAllStats(date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m") - 12, date("d"), date("Y")) ),'month', 'all') ;

    // ------------------------------

    $country_downloads  = ModelMarket::newInstance()->getDownloadsByCountry('all', date( 'Y-m-d H:i:s',  mktime(0, 0, 0, date("m") - 10, date("d"), date("Y")) )) ;

?>
</div>
</div>
<style>
    .widget-box-content {
        height: 230px;
        overflow:hidden;
        clear:both;
    }
    .widget-box-title{
        height: 21px
    }
    .widget-box-title .tabs{
        float:right;
        margin:0px 0px 0 0;
        padding:0;
    }
    .widget-box-title .tabs li{
        float:left;

        border: 1px solid #DDD;
        border-bottom:0px;
        background: #E6E6E6;
        font-weight: normal;
        color: #212121;
        margin:0 2px 0 0px;
        border-radius: 4px 4px 0 0;
        -moz-border-radius: 4px 4px 0 0;
        -webkit-border-radius: 4px 4px 0 0;
    }
    .widget-box-title .tabs li.active{
        border-color:#DDD;
        background: #fff;
        color: #212121;
    }
    .widget-box-title .tabs li a{
        float: left;
        padding:6px 10px;
        text-decoration: none;
        font-size:13px;
        color:#555;
    }
    .widget-box-title h3.has-tabs{
        float:left;
    }
</style>
<div class="market-dashboard">

    <div class="grid-row grid-100">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Downloads by Country', 'market'); ?></h3>
                </div>
                <div class="widget-box-content" style="height: 500px;">
                    <b class="stats-title"></b>
                    <div id="placeholder_map" class="graph-placeholder" style="width:100%; height: 100%;">
                        <?php if( count($country_downloads) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title">
                    <h3><?php _e('Downloads', 'market'); ?></h3>
                </div>
                <div class="widget-box-content">
                    <b class="stats-title"></b>
                    <div id="placeholder" class="graph-placeholder" style="height:90%">
                        <?php if( count($total_downloads) == 0 ) {
                            _e("There're no statistics yet") ;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="grid-row grid-first-row grid-50">
        <div class="row-wrapper">
            <div class="widget-box">
                <div class="widget-box-title"><h3 class="has-tabs"><?php _e('Top downloads', 'market'); ?></h3>
                    <ul class="tabs">
                        <?php foreach($aType as $k => $v) { ?>
                        <li><a href="#top-<?php echo $k; ?>"><?php echo $v; ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="widget-box-content">
                    <?php
                    foreach($aType as $k => $v) { ?>
                    <div id="top-<?php echo $k; ?>">
                        <table class="table" cellpadding="0" cellspacing="0">
                            <tbody>
                            <thead>
                                <th><?php _e('Name','market');?></th>
                                <th><?php _e('Downloads','market'); ?></th>
                            </thead>
                            <?php
                            $aux_array = $aTop[$k];
                            if(count($aux_array) > 0) {
                                foreach( $aux_array as $aux) {
                                View::newInstance()->_exportVariableToView('item', $aux) ; ?>
                                <tr>
                                    <td class="children-cat"><a href="<?php echo osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'stats.php').'?itemId='.osc_item_id(); ?>"><?php echo osc_item_title();?></a></td>
                                    <td><?php echo $aux['total'];?></td>
                                </tr>
                            <?php }
                            } ?>
                            </tbody>
                        </table>
                        <p class="view-all"><a href="<?php echo osc_admin_render_plugin_url("market/stats.php").'&iStatus='.$k ?>"><?php _e('View all','market') ?> <?php echo $v;?></a></p>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
$(document).ready(function(){
    $('.widget-box-title .tabs li').each(function(){
        var dest = function(el){
            var dests = new Array();
            el.parent().find('a').each(function(){
                dests.push($(this).attr('href'));
            });
            return dests.join(',');
        }

        $(this).click(function(){
            $(this).parent().children().removeClass('active').filter($(this).addClass('active'));
            $(dest($(this))).hide().filter($(this).children().attr('href')).show();
            return false;
        });
    }).filter(':first').click();
    $('.widget-box-title .tabs').each(function(){
        $(this).find('li:first').click();
    })
});
</script>



<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

    google.load('visualization', '1', {'packages': ['geochart']});
    google.setOnLoadCallback(drawRegionsMap);


    /* country map */
    function drawRegionsMap()
    {
        var data = new google.visualization.DataTable();
        data.addColumn('string', '<?php echo osc_esc_js(__('Country')); ?>');
        data.addColumn('number', '<?php echo osc_esc_js(__('Total downloads')); ?>');

        <?php
        $acomulate = 0 ;
        $k = 0 ;
        echo "data.addRows(" . count($country_downloads) . ");" ;

        foreach($country_downloads as $k => $aux) {
            echo "data.setValue(" . $k . ', 0, "' . $aux['s_country_code']. '");';
            echo "data.setValue(" . $k . ", 1, " . $aux['num'] . ");";
        }
        ?>


        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('placeholder_map'));
        chart.draw(data, options);
    }
</script>

<script type="text/javascript">

    google.load('visualization', '1', {'packages':['corechart']});
    google.setOnLoadCallback(drawChart);


    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    function drawChart() {
        /* AreaChart */
        var data = new google.visualization.DataTable();
        data.addColumn('string', '<?php echo osc_esc_js(__('Date')); ?>');
        data.addColumn('number', '<?php echo osc_esc_js(__('Total downloads')); ?>');
        data.addColumn('number', '<?php echo osc_esc_js(__('oc-admin downloads')); ?>');

        <?php
        $acomulate = 0 ;
        $k = 0 ;
        echo "data.addRows(" . count($total_downloads) . ");" ;

        foreach($total_downloads as $k => $aux) {
            echo "data.setValue(" . $k . ', 0, "' . $aux['d_date']. '");';
            echo "data.setValue(" . $k . ", 1, " . $aux['num'] . ");";
            echo "data.setValue(" . $k . ", 2, " . $aux['ocadmin_num'] . ");";
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

    <div class="grid-row grid-first-row grid-100">
        <div class="row-wrapper">