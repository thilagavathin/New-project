<?php 
include 'templates/header.php'; 


//Agency Mapping Query
    $user_agency=array();
    $sql_agency="SELECT agency_id FROM agency_map WHERE user_id=".$_SESSION['adminlogin'];
    $sql_agency_row = mysql_query($sql_agency);
    while($agency_row=mysql_fetch_array($sql_agency_row)) {
    $user_agency[]=$agency_row['agency_id'];
    }
    $user_agency=array_unique($user_agency);
    if(count($user_agency)!=0){
       $user_agency_ids=implode(',',$user_agency); 
    }else{
        $user_agency_ids='0';
    }
?>
     		<section >
	     		<div class="container">
				<div class="row">
					<div class="col-md-12">
						<ol class="breadcrumb">
						  <li><a href="systemdashboard.php">Dashboard</a></li>
						  <li class="active">Announcements </li>              
						</ol>
					</div>
				 </div>
	     			<div class="row">
                <div class="col-sm-12">
                  <h1 class="page-title">Announcements <small class="text_black fb_300">(Sort and Select Recipients)</small></h1>
                </div>
            </div>
             <!-- filter form -->
           <div class="row announcement_msgbox">
              <div class="col-md-12 col-sm-8 col-xs-12 ">
                   <div class="chat_window">
                      <div class="chat_box">
                        <div class="chat_area">
                          <ul>
                          <?php
                            $announce_date_array=array();
                            $announce_sql="SELECT * FROM announcements WHERE agency_id=0 OR agency_id IN (".$user_agency_ids.") order by id asc";
                        	$announce_qry = mysql_query($announce_sql);
                            while($announce_row=mysql_fetch_array($announce_qry)) {
                                
                            //Set view count
                            $readers=array();                            
                            if($announce_row['read_user_id']!=''){
                                $readers=unserialize($announce_row['read_user_id']);
                            }
                            if(!in_array($_SESSION['adminlogin'],$readers)){
                                $readers[] = $_SESSION['adminlogin'];
                            }  
                            $reader_ids=serialize($readers);
                            
                            $update_query="UPDATE `announcements` SET  `read_user_id` =  '".$reader_ids."' WHERE `id` ='".$announce_row['id']."' ";
                            mysql_query($update_query);    
                                
                            
                            //User profile Image Start
                            $sql_user = "SELECT * FROM `login_users` WHERE `user_id` = '".$announce_row['user_id']."'";
                            $result_user = mysql_query($sql_user) or die(mysql_error());
                            $row = mysql_fetch_array($result_user);
                            
                            if($row['user_image']<>'') $user_img=@unserialize($row['user_image']);
                            else $user_img='';
                            if($user_img=='')  $img_val ="assets/img/photo.jpg";
                            else $img_val ="assets/profile/".$user_img[0];
                            //User profile Image end  
                            $announce_date = date('M d, Y',strtotime($announce_row['date']));
                            if(! in_array($announce_date,$announce_date_array)){
                                $announce_date_array[]=$announce_date;
                                //start date separator -->
                                echo '<p class="date_divider"><span class="text_blue fb_500">'.$announce_date.'</span></p>';
                                //end date separator --> 
                            }
                            
                                ?>
                              <!-- left comment area -->
                               <li class="row left-comment text-left">
                                  <div class="col-md-1 col-sm-2 col-xs-2">
                                      <img src="<?php echo $img_val; ?>" alt="profile icon" class="profile-image">
                                  </div>
                                  <div class="col-md-11 col-sm-10 col-xs-9 pad_l20">
                                      <div class="comment-info">
                                         <p><span class="chat-username"><?php echo $announce_row['user_name'] ?></span><small class="chat-datelocation"><i><?php echo date('h:i a',strtotime($announce_row['date']));  ?></i></small></p>
                                         <span class="clearfix"></span>
                                         <p class="chat-content mar_tb10"><?php echo $announce_row['comments'] ?>
                                         </p>
                                      </div>
                                  </div>
                               </li>
							<?php } ?>
								                                
                              </ul>
                          </div>
                          
                      </div>
                   </div>
                </div>
           </div>
            </div>
	     		</div>
     		</section>
<?php include 'templates/footer.php'; ?>
	<script type="text/javascript">
    $(document).ready(function(){
        $('.count').each(function () {
            $(this).prop('Counter',0).animate({
                Counter: $(this).text()
            }, {
                duration: 4000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });
    </script>
    <script type="text/javascript">
    $(document).ready(function(){
           
        $('#accordion .panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
          });
        $('#accordion .panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
          });
    });
    </script>
    <!-- slim scroll for attachment panels -->
    <script type="text/javascript">
      $('.attachment_list').slimScroll({
        position: 'right',
        height: '145px',
        railVisible: true,
        alwaysVisible: false
    });
      $('.chat_area').slimScroll({
        position: 'right',
        height:'480px',
        start:'bottom',
        railVisible: true,
        alwaysVisible: false

    });
      $(".chat_box").height(480);
    </script>
   
  </body>
</html>
