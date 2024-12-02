const REMOTE_SERVER = "../realKwbot";


function connectMain(){
    const data_t = "vb=nm";
    $.get(REMOTE_SERVER, data_t, function(cv){
         $("#dispdisprealTime").html(cv);
    });
}

function navigateTo(url){
    $("#KWBOT_ALL_CONTAINER")
        .html(LOADING_S_)
        .load(`functions/SOP/php/${url}.php`);
}

function plotArea(date,numbers){
    
}