<!--START QUICKVIEW -->
<div id="quickview" class="quickview-wrapper" data-pages="quickview">

    <ul class="no-style m-t-40 padding-30">
        <li><a href="settings.php"><i class="sl-settings"></i> Settings</a>
        </li>
        </li>
        <li><a href="#"><i class="sl-question"></i> Help</a>
        </li>
        <li class="m-t-20">
            <a href="logout.php" class="clearfix">
                <span class="pull-left">Logout</span>
                <span class="pull-right"><i class="sl-logout"></i></span>
            </a>
        </li>
    </ul>
    <a class="btn btn-default quickview-toggle" data-toggle-element="#quickview" data-toggle="quickview"><i class="pg-close"></i></a>

</div>
<!-- END QUICKVIEW-->
<!-- BEGIN VENDOR JS -->
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/plugins/boostrapv3/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-bez/jquery.bez.min.js"></script>
<script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script type="text/javascript" src="assets/plugins/bootstrap-select2/select2.min.js"></script>
<script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
<script src="assets/plugins/switchery/js/switchery.min.js" type="text/javascript"></script>

<script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END VENDOR JS -->
<script src="assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-datatable/extensions/FixedHeader/js/dataTables.fixedHeader.js"></script>
<script type="text/javascript" src="assets/plugins/datatables-responsive/js/lodash.min.js"></script>
<!-- BEGIN CORE TEMPLATE JS -->
<script src="pages/js/pages.min.js"></script>
<!-- END CORE TEMPLATE JS -->
<!-- BEGIN PAGE LEVEL JS -->
<script src="assets/js/portlets.js" type="text/javascript"></script>
<script src="assets/js/datatables.js" type="text/javascript"></script>
<script src="assets/js/scripts.js" type="text/javascript"></script>
<!-- END PAGE LEVEL JS -->
<!-- success message -->
<script>
    $( document ).ready(function() {
        $("#infor").slideUp( 3000 ).delay( 4000 ).fadeOut( 400 );
    });
</script>
<!-- sucess message -->
<script>

</script>
<script type="text/javascript">
    $(document).ready(function() {
            function update() {
            var formData = "";
            $.ajax({
                url: "get_message_chat_count.php",
                type: "POST",
                data: formData,
                cache: false,
                success: function (html) {
                    var $success = $.trim(html);
                    if ($success != "0") {
                        $("#msg_chat_count").html($success);
                    }
                }
            });

            $.ajax({
                url: "getDashboardNew.php",
                type: "POST",
                data: formData,
                cache: false,
                success: function (html) {
                    var $success = $.trim(html);
                    if ($success != "0") {
                        $("#dashboard_new_count").html($success);
                    }
                }
            });

            $.ajax({
                url: "getReportDashboardCount.php",
                type: "POST",
                data: formData,
                cache: false,
                success: function (html) {
                    var $success = $.trim(html);
                    if ($success != "0") {
                        $("#get_rd_count").html($success);
                    }
                }
            });
        }

    });


</script>
<script src="assets/js/drawer.js" type="text/javascript"></script>
    <script type="text/javascript">
       $('#drawerExample').drawer({ toggle: false });
       $('#other-toggle').click(function() {
       $('#drawerExample').drawer('toggle');
         return false;
       });
    </script>
    <script type="text/javascript">
function show_section(){
		$( "li" ).find( "ul.sum1" ).css( "display", "block" );
		$( "li" ).find( "ul.sum2" ).css( "display", "none" );
		$(".arr1").attr('onclick','hide_section()');
		$(".arr2").attr('onclick','show_section1()');
  }
  function hide_section(){
		$( "li" ).find( "ul.sum1" ).css( "display", "none" );
		$(".arr1").attr('onclick','show_section()');
  }
  function show_section1(){
		$( "li" ).find( "ul.sum2" ).css( "display", "block" );
		$( "li" ).find( "ul.sum1" ).css( "display", "none" );
		$(".arr1").attr('onclick','show_section()');
		$(".arr2").attr('onclick','hide_section1()');
  }
  function hide_section1(){
		$( "li" ).find( "ul.sum2" ).css( "display", "none" );
		$(".arr2").attr('onclick','show_section1()');
  }
  </script>
</body>
</html>
