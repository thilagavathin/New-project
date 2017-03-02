<?php 
include 'templates/header.php';
if($_SESSION['userrole']==3 || $_SESSION['userrole']==2) {
    header('Location:dashboard.php'); die;
}
$filter_region=isset($_POST['region'])? $_POST['region']:'';
$filter_agency=isset($_POST['agency'])? $_POST['agency']:'';

//insert query

if(isset($_POST['submit_announce'])){
    $msg_date=date('Y-m-d  H:m:i');
    $insert_sql="INSERT INTO announcements(region,agency_id,user_id,user_name,comments,attachement,date) VALUES ('".$_POST['region_hidden']."','".$_POST['agency_hidden']."','".$_SESSION['adminlogin']."','".$_SESSION['adminlogin1']."','".$_POST['announce_text']."','','".$msg_date."')";
    mysql_query($insert_sql);
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
             <form method="post">
            <div class="row mar_t10 mar0 filter form">
              <div class="col-md-4 col-sm-4 col-xs-12 pad_l0">
                  <div class="form-group">
                      <label class="ft_17">By Region</label>
                      <select class="form-control region" id="region" name="region" onchange="region_name(this.value)">
                          <option value="">All</option>
                          <option <?php if($filter_region=='R-1') { echo 'selected'; }  ?> value="R-1">R-1</option>
                          <option <?php if($filter_region=='R-2') { echo 'selected'; }  ?> value="R-2">R-2</option>
                          <option <?php if($filter_region=='R-3') { echo 'selected'; }  ?> value="R-3">R-3</option>
                          <option <?php if($filter_region=='R-4') { echo 'selected'; }  ?> value="R-4">R-4</option>
                          <option <?php if($filter_region=='R-5') { echo 'selected'; }  ?> value="R-5">R-5</option>
                          <option <?php if($filter_region=='R-6') { echo 'selected'; }  ?> value="R-6">R-6</option>
                        </select>
                  </div>
              </div>
              <div class="col-md-5 col-sm-4 col-xs-12 pad_r20">
                  <div class="form-group">
                      <label class="ft_17">By Agency</label>
                      <select class="form-control" name="agency" id="agency" onchange="agency_name(this.value)">
                        <option value="">All</option>
                        <?php
                        $sql="SELECT distinct(name),id FROM agency GROUP BY name order by name asc";
                    	$agency_list = mysql_query($sql);
                        while($agency_row=mysql_fetch_array($agency_list)) { ?>
                        <option value="<?php echo $agency_row['id']; ?>" <?php echo ($filter_agency==$agency_row['id'])? 'selected':''; ?> ><?php echo $agency_row['name']; ?></option>
                        <?php }   ?>
                      
                      </select>
                  </div>
              </div>
              <div class="col-md-3 col-sm-4 col-xs-12 pad0">
                  <div class="form-group">
                      <label class="ft_17">&nbsp;</label>
                      <button name="filter" class="wid100 mar_t0">Search</button>
                  </div>
              </div>
            </div> 
            </form> 

            <div class="row announcement_chatbox">
                <div class="col-md-9 col-sm-8 col-xs-12 pad0">
                   <div class="chat_window">
                   <form name="announce_form" method="post" enctype="multipart/form-data" onsubmit="return submit_annoucement();"> 
                      <div class="chat_box">
                        <div class="chat_area">
                          <ul>
                          <?php
                            $where='';
                            if($filter_region!=''){
                               $where.=" AND region='".$filter_region."' "; 
                            }
                            if($filter_agency!=''){
                               $where.=" AND agency_id='".$filter_agency."' "; 
                            }
                            $announce_date_array=array();
                            $announce_sql="SELECT * FROM announcements WHERE send_status=0 ".$where." order by id asc";
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
                            <?php }  ?>   
                               
                               <!-- left comment area -->
                               
                              </ul>
                          </div>
                          <div class="comment-box">
                               
                              <div class="row">
                                  <div class="col-md-8 col-sm-8 col-xs-7 pad_r0">
                                      <div class="form-group mar0">
                                          <input type="text" class="form-control" name="announce_text" id="announce_text">
                                      </div>
                                  </div>
                                  <div class="col-md-1 col-sm-1 col-xs-2 mar_t10">
                                      <span><i class="fa  fa-smile-o text_blue"></i></span>
                                      <div class="custom-fileupload mar_t5">
                                        <label>
                                          <i class="fa fa-paperclip "></i> 
                                          <input type="file" class="form-control hidden" >
                                        </label>
                                      </div>
                                  </div>
                                  <div class="col-md-3 col-sm-3 col-xs-3 form pad_l0">
                                     <input type="hidden" name="region_hidden" id="region_hidden" />
                                     <input type="hidden" name="agency_hidden" id="agency_hidden" />
                                     <button id="submit_announce" name="submit_announce" class="button mar0" data-announcementstaus="Please check Recipients and Proof Message">Send</button>
                                     
                                  </div>
                              </div>
                              
                          </div>
                      </div>
                      </form>
                   </div>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12 pad0">
                  <div class="chat_userlist">
                      <h3 class="text_blue mar0 pad0 fb_300">Announcement Status</h3>
                      <p id="announcement_satus">Please carefully review intendned reciepients and proof message, then press send</p>
                      <div class="text-center announcement_button">
					  
                        <button id="pre_send" class="button" type = "button" data-announcementstaus="Please check Recipients and Proof Message" >Preview Announcement</button>
                        <button onclick="reset_announcement()" class="button" type = "button" data-announcementstaus="Page will go revert back to the starting page , all sorts go to neutral position">Reset</button>
                        <input type="hidden" value="0" id="pre_send_status" />
                      </div>
                  </div>
                </div>
            </div>
	     		</div>
     		</section>

<?php include 'templates/footer.php'; ?>
    <script type="text/javascript">
      function submit_annoucement(){
        var announce_text=$("#announce_text").val();
        var region=$("#region_hidden").val();
        var agency=$("#agency_hidden").val();
        var pre_send_status=$("#pre_send_status").val();
        if(announce_text==''){
            alert("Please fill announcement");
            $("#announce_text").focus();
            return false;
        }
        if(pre_send_status==0){
            alert('Please check Recipients, and Proof Message and click "Preview Announcement" button, then click "Send"');
            return false;
        }
       
      }
      function region_name(name){
        $("#region_hidden").val(name);
      }
      function agency_name(name){
        $("#agency_hidden").val(name);
      }
      function reset_announcement(){
        var reset=confirm('Page will go revert back to the starting page , all sorts go to neutral position');
       
        if(reset==true){
            window.location.href='announcements.php';
        }
      }
    
      $("#pre_send").click(function(){
         $("#pre_send_status").val(1);         
         $("#submit_announce").attr('data-announcementstaus','Message Has Been Sent');      
      })
            
    //slim scroll for attachment panels -->
      
    $('.attachment_list').slimScroll({
        position: 'right',
        height: '145px',
        railVisible: true,
        alwaysVisible: false
    });
    $('.chat_area').slimScroll({
        position: 'right',
        height:'300px',
        start:'bottom',
        railVisible: true,
        alwaysVisible: false
    
    });
    
    </script>
	
	<script type="text/javascript">
    $(document).ready(function(){
      var maxHeight = 0;
        for(i=1;i<=2;i++){
        $("#assign_box_itmes" + i).each(function(){
           $(this).find(".item_info ul li ul").each(function(){
               if ($(this).height() > maxHeight) { maxHeight = $(this).height(); }
            });
           $(this).find(".item_info ul li ul").height(maxHeight);
            
        });
        }
      });
    </script>

    <script type="text/javascript">
      
      $("button").click(function(){

         var announcement_satus = $(this).attr("data-announcementstaus");

         $("#announcement_satus").html(announcement_satus);

      })

    </script>


  </body>
</html>
