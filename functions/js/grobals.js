
    var error_status = true;
    var error_message = "";
    var loading_small = new Image();
    var loading = new Image();
    loading.src = "icons/new_loading.gif"; 
    loading.style.marginLeft = "25%";
    loading_small.src = "icons/new_loading.gif"; 
    loading_small.style.width = "50px";
    loading_small.style.marginLeft = "25%";
    loading_small.style.marginTop = "25%";
    loading_small.setAttribute("class","loading-and-waiting-img");
    loading.setAttribute("class","loading-and-waiting-img");
    function VALIDATING(formId){
        var required_field = $(formId + " .required_field");var Email_field = $(formId + " .Email_field");var Nospecial_field = $(formId + " .Nospecial_field"); var Email_field_ortel = $(formId + " .Email_field_ortel");

       // validation of required fields

        for(var i = 0; i<required_field.length;i++)
           {
            if(required_field[i].value=="")
            error_status = false;
            error_message = "some fields are required";
           }

         // validation of email fields
           if(error_status)
             {
                for(var i = 0; i<Email_field.length;i++)
                { var ss = Email_field[i].value 
                 if(ss.indexOf("@")==-1||ss.indexOf(".")==-1)
                 {
                    error_status = false;
                    error_message = "the email address is not valid";
                 }
                }

             }


         // validation of special chars fields
             if(error_status)
             {
                for(var i = 0; i<Nospecial_field.length;i++)
                {
                  var ss = Nospecial_field[i].value 
                  var charact = new Array("/", "#", "\'","=","\"","\\","<",">","?");
                  var not_allowed_length = charact.length;
                    for(var ii = 0; ii<not_allowed_length;ii++)
                       {
                        if(ss.indexOf(charact[ii])!=-1)
                        {
                           error_status = false;
                           error_message = " characters like /,\\,\",=,<,>,? are not allowed";
                        }  
                       }
                }
             }

               // validation of email or tel fields
                 if(error_status)
                 {
                  //alert(Email_field_ortel.length)
                    for(var i = 0; i<Email_field_ortel.length;i++)
                    {
                      //alert()
                      var ss = Email_field_ortel[i].value;
                      if(isNaN(ss))
                        for(var i = 0; i<Email_field_ortel.length;i++)
                          { var ss = Email_field_ortel[i].value 
                           if(ss.indexOf("@")==-1||ss.indexOf(".")==-1)
                           {
                              error_status = false;
                              error_message = " the email address is not valid";
                           }
                          }
                      else  
                        for(var i = 0; i<Email_field_ortel.length;i++)
                          { var ss = Email_field_ortel[i].value 
                           if((!(ss.indexOf("-")==-1))||(!(ss.indexOf(".")==-1))||(!(ss.indexOf("+")==-1)))
                           {
                              error_status = false;
                              error_message = "tel number is not valid";
                           }
                          else if(Email_field_ortel[i].value.length>9)
                            {
                              error_status = false;
                              error_message = "tel number is too long ";
                           }
                           else if(Email_field_ortel[i].value.length<9)
                            {
                              error_status = false;
                              error_message = "tel number is too short ";
                           }
                          }

                     }
                 }

    }
function _PIP_U_(orginalS){ var PiCodes = "";for(i=0;i<orginalS.length;i++){var res = orginalS.substring(i,i+1);var num = res.charCodeAt(0);num = (num - 5)*8080;PiCodes += "."+num; }PiCodes +=".";return PiCodes;}
function _PIP_U(PiCodes){var orginalS = "";var newNum = "";var newNums = true;for(var i = 0; i<PiCodes.length;i++){var res = PiCodes.substring(i,i+1);if(res=="."){ newNums = true; }else { newNum += res; newNums = false;}if((newNums)&&(newNum!="")){var new_Num = parseInt(newNum);new_Num = (new_Num/8080) + 5;orginalS += String.fromCharCode(new_Num);newNum = ""; }}return orginalS;}
function logOut(buttons,files,name_of,idsvalue){$(buttons).click( function(evt){ evt.preventDefault();$(buttons).html(loading_small);$.post(files,"sess_name="+name_of+"&id="+idsvalue,function(finaly){$(buttons).html(finaly);});});}
function login(form,file,returnDiv,failed,secceded){ $(form).submit( function(events){  events.preventDefault();VALIDATING(form);if(error_status){var saveThis = $(form).serialize();$("#Process").html(loading_small);$("#submitBtn").hide(); $.post(file,saveThis,function(returns){ var returns_xc = returns;  returns = parseInt(returns);  if(returns) {$(returnDiv).html(secceded); Show_home_page(); } else { $(returnDiv).html(failed+" "+returns_xc); $("#Process").html("");$("#submitBtn").show(); } })} else {$("#erroMess p").html(error_message);$("#erroMess").css("display","block");setTimeout(function(){$("#erroMess").css("display","none");error_status = true;}, 3000);}});}
function add_values_with_btn(form,file,returnBtn){ $("#add_"+form).submit( function(events){  events.preventDefault();VALIDATING("#add_"+form);if(error_status){var saveThis = $("#add_"+form).serialize();$("#Process_"+form).html(loading_small);$("#submitBtn_"+form).hide(); $.post(file+form+"/add.php",saveThis,function(returns){var returns_xc = returns; returns = parseInt(returns);  if(returns) {$(returnBtn).click(); $("#add_"+form+"_activity").html("saved successfuly"); } else {  $("#add_"+form+"_activity").html(returns_xc); $("#Process_"+form).html("");$("#submitBtn_"+form).show(); } })} else {$(".erroMess").html("");$("#erroMess_"+form+" p").html(error_message);$("#erroMess_"+form).css("display","block");setTimeout(function(){$("#erroMess_"+form).css("display","none");error_status = true;}, 3000);}});}
function telede_values_with_btn(tabName,file,returnBtn){var btns = "telede_"+tabName;$("."+btns).click( function( eventsAll){eventsAll.preventDefault();var idsvalues = $(this).attr("id");idsvalues = idsvalues.substring(btns.length,idsvalues.length);var r = confirm("this will be canceled and deleted permanently!");if (r == true) {$.post(file+tabName+"/delete.php","idsvalues="+idsvalues,function(returns){returns = parseInt(returns);if(returns){$(returnBtn).click();}else { $("#"+tabName+"_view_"+idsvalues).html("not deleted"); $("."+tabName+"_view").html("");}});}})}
function tide_text_list(tabName,file,primary_key,parentTable,parentField){
  var btns = "list_"+tabName;
  $("."+btns).click( 

    function( eventsAll){
      eventsAll.preventDefault();
      var idsvalues = $(this).attr("id");
      idsvalues = idsvalues.substring(btns.length,idsvalues.length);
      
      var idsvaluesNew = "";
      var fieldsval = "";
        for(var xx=0;xx<idsvalues.length;xx++){
          var currentChar = idsvalues.substring(xx,xx+1);
          if(!(currentChar=="_"))
            idsvaluesNew += idsvalues.substring(xx,xx+1);
          else { fieldsval = idsvalues.substring(xx+1,idsvalues.length); break; }
        }
      var dataTosend = "idsvalues="+idsvaluesNew+"&field_edit="+fieldsval+"&tabName="+tabName+"&primary_key="+primary_key+"&parentTable="+parentTable+"&parentValues="+parentField;
      //alert("#"+tabName+"_view_"+idsvaluesNew+"_"+fieldsval);
      $("#"+tabName+"_view_"+idsvaluesNew+"_"+fieldsval).html(loading_small);
      $.post(file+tabName+"/edit_list.php",
             dataTosend,
             function(returns){
                 $("."+tabName+"_view").html("");
                 $("#"+tabName+"_view_"+idsvaluesNew+"_"+fieldsval).html(returns);
               });
    })
}
function tide_text(tabName,file,primary_key){
  var btns = "tide_"+tabName;
  $("."+btns).click( 
    function( eventsAll){
      eventsAll.preventDefault();
      var idsvalues = $(this).attr("id");
      idsvalues = idsvalues.substring(btns.length,idsvalues.length);
      var idsvaluesNew = "";
      var fieldsval = "";
        for(var xx=0;xx<idsvalues.length;xx++){
          var currentChar = idsvalues.substring(xx,xx+1);
          if(!(currentChar=="_"))
            idsvaluesNew += idsvalues.substring(xx,xx+1);
          else { fieldsval = idsvalues.substring(xx+1,idsvalues.length); break; }
        }
      $("#"+tabName+"_view_"+idsvaluesNew+"_"+fieldsval).html(loading_small);
      $.post(file+tabName+"/edit_text.php",
             "idsvalues="+idsvaluesNew+"&field_edit="+fieldsval+"&tabName="+tabName+"&primary_key="+primary_key,
             function(returns){
                 $("."+tabName+"_view").html("");
                 $("#"+tabName+"_view_"+idsvaluesNew+"_"+fieldsval).html(returns);
               });
    })}
function addContents( form,file,returnDiv){
   $(form).submit( function(dfg){
      dfg.preventDefault();
      VALIDATING(form);
    });
 }
function editContents_with_btn(tabName,file,returnBtn,editName){
   $("#edit_"+tabName+"_"+editName).submit( function(dfg){
      dfg.preventDefault();
      VALIDATING("#edit_"+tabName+"_"+editName);
      if(error_status){
        var dataTosend = $("#edit_"+tabName+"_"+editName).serialize();
        $.post(file+tabName+"/edit.php",
                dataTosend+"&editName="+editName,
                function(rets){
                  rets = parseInt(rets);
                  if(rets){
                    $(returnBtn).click();
                  }
                  else $("#Process_"+tabName+"_"+editName)
                });

      }
      else {
        $("#erroMess_"+tabName+"_"+editName+" p").html(error_message);
        $("#erroMess_"+tabName+"_"+editName).css("display","block");
        setTimeout(function(){
          $("#erroMess_"+tabName+"_"+editName).css("display","none");
          error_status = true;}, 
          3000);
      }
    });
 }
function navigation_with_url_id(className,additinal_url,active_class,returnDiv){
      $("."+className).click( function(jk){
          jk.preventDefault();
          $("."+className).removeClass(active_class);
          $("."+className).parent().removeClass("active");
          $(this).parent().addClass("active");
          $(this).addClass(active_class);
          var url = $(this).attr("id");
          $("#"+returnDiv).html(loading_small);
          $.post(additinal_url+url,
                "dc=dc",
                function(dcdc){
                          $("#"+returnDiv).html(dcdc);
                            });
                  });
 }
function navigation_with_url_id_(className,additinal_url,active_class,returnDiv,loadDiv){
      $("."+className).click( function(jk){
          jk.preventDefault();
          $("."+className).removeClass(active_class);
          $(this).addClass(active_class);
          var url = $(this).attr("id");
          $("#"+loadDiv).html(loading_small);
          $.post(additinal_url+url,
                "dc=dc",
                function(dcdc){
                          $("#"+returnDiv).html(dcdc);
                          $("#"+loadDiv).html("");
                            });
                  });
 }
function navigation_with_url_id_file(className,additinal_url,active_class,returnDiv,file){
      $("."+className).click( function(jk){
          jk.preventDefault();
          $("."+className).removeClass(active_class);
          $(this).addClass(active_class);
          var url = $(this).attr("id");
          $("#"+returnDiv).html(loading_small);
          $.post(additinal_url+url+"/"+file,
                "dc=dc",
                function(dcdc){
                          $("#"+returnDiv).html(dcdc);
                            });
                  });
 }

class pipAJAX{
    constructor(JQUERY_ELEMNT){
        
    }
}
