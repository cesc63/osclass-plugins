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

     $id = Params::getParam('id');
     switch(Params::getParam('bulk_actions')) {
         case 'disable':
             foreach($id as $_id) {
                Universe::newInstance()->disable($_id);
             }
             break;
         case 'enable':
             foreach($id as $_id) {
                Universe::newInstance()->enable($_id);
             }
             break;
         case 'delete':
             foreach($id as $_id) {
                 $file = Universe::newInstance()->findByPrimaryKey($_id);
                 @unlink($file['s_file']);
                 Universe::newInstance()->delete($_id);
             }
             break;
         default:
             break;
     }

?>
<script type="text/javascript">
    $(function() {
        oTable = new osc_datatable();
        oTable.fnInit({
            'idTable'       : 'datatables_list',
            "sAjaxSource": "<?php echo osc_base_url(true); ?>?page=ajax&action=custom&ajaxfile=<?php echo osc_plugin_folder(__FILE__) . 'ajax.php';?>&paction=files",
            'iDisplayLength': '10',
            'iColumns'      : '5',
            'oLanguage'     : {
                    "sInfo":         "<?php _e('Showing _START_ to _END_ of _TOTAL_ entries') ; ?>"
                    ,"sZeroRecords":  "<?php _e('No matching records found') ; ?>"
                    ,"sInfoFiltered": "(<?php _e('filtered from _MAX_ total entries') ; ?>)"
                    ,"oPaginate": {
                                "sFirst":    "<?php _e('First') ; ?>",
                                "sPrevious": "<?php _e('Previous') ; ?>",
                                "sNext":     "<?php _e('Next') ; ?>",
                                "sLast":     "<?php _e('Last') ; ?>"
                            }
            },
            "aoColumns": [
                {"sTitle": "<div style='margin-left: 8px;'><input id='check_all' type='checkbox' /></div>", 
                 "bSortable": false, 
                 "sClass": "center", 
                 "sWidth": "10px",
                 "bSearchable": false
                 },
                {"sTitle": "<?php _e('Slug'); ?>","bSortable": true},
                {"sTitle": "<?php _e('Version'); ?>" },
                {"sTitle": "<?php _e('Enable / Disable'); ?>","bSortable": true},
                {"sTitle": "<?php _e('Attached to'); ?>","bSortable": true},
                {"sTitle": "<?php _e('Download'); ?>"},
                {"sTitle": "<?php _e('# downloads'); ?>"},
                {"sTitle": "<?php _e('Delete'); ?>"}
            ]
        });
    });

    $('#datatables_list tr').live('mouseover', function(event) {
        $('#datatable_wrapper', this).show();
        $('#datatables_quick_edit', this).show();
    });

    $('#datatables_list tr').live('mouseleave', function(event) {
        $('#datatable_wrapper', this).hide();
        $('#datatables_quick_edit', this).hide();
    });

</script>
<div>
    <form id="datatablesForm" action="<?php echo osc_admin_base_url(true); ?>" method="post">
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>general.php" />
        <input type="hidden" name="paction" value="bulk_actions" />
        <div style="clear:both;"></div>

        <div class="top" style="margin-top:10px;">
            <div style="float:left;"><?php _e('Show') ; ?>
                <select class="display" id="select_range">
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="100">100</option>
                </select> <?php _e('entries') ; ?>
            </div>
            <div id="TableToolsToolbar">
                <select id="bulk_actions" name="bulk_actions" class="display">
                    <option value=""><?php _e('Bulk actions'); ?></option>
                    <option value="enable"><?php _e('Enable') ?></option>
                    <option value="disable"><?php _e('Disable') ?></option>
                    <option value="delete"><?php _e('Delete') ?></option>
                </select>
                &nbsp;<button id="bulk_apply" class="display"><?php _e('Apply') ?></button>
            </div>
        </div>
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="datatables_list"></table>
    </form>
</div>
<script>
    $('#check_all').live('change',
        function(){
            if( $(this).attr('checked') ){
                $('#'+oTable._idTable+" input").each(function(){
                    $(this).attr('checked','checked');
                });
            } else {
                $('#'+oTable._idTable+" input").each(function(){
                    $(this).attr('checked','');
                });
            }
        }
    );

</script>